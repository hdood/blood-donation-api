<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Question;
use App\Notifications\AppointmentAccepted;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{

    public function appointmentRequests()
    {

        $appointments = Appointment::with('donor')->with(['questions' => function ($query) {
            $query->withPivot('data', 'id');
        }])->get();

        return $appointments;
    }
    public function todayAppointments()
    {
        return Appointment::where(['confirmed' => 1])->whereDate('date', Carbon::today())->whereTime('time', ">=", Carbon::now())->with('donor')->orderBy("created_at", 'desc')->get();
    }
    public function ScheduledAppointments()
    {
        return Appointment::where(['confirmed' => 1])->whereDate('date', '>=', Carbon::today()->format("y-m-d"))->with('donor')->orderBy("date", 'asc')->get();
    }

    public function book(Request $request)
    {
        $data = $request->validate([
            'donor_id' => 'required',
            'answers' => 'required'
        ]);
        $appointment = Appointment::create(['donor_id' => $data['donor_id']]);

        if ($appointment) {
            $answers = (array) json_decode($data['answers']);
            foreach ($answers as $answer) {
                $answer = (array) $answer;
                $appointment->questions()->attach($answer['id'], ['data' => $answer['data']]);
            }
            return response()->json(["error" => false]);
        }
        return response()->json(["error" => "something went wrong"], 500);
    }

    public function getAppointmentQuestions()
    {
        return Question::all();
    }

    public function acceptAppointment(Request $request)
    {
        $data = $request->validate([
            'id' => 'required',
            'time' => 'required',
            'date' => 'date'
        ]);


        $appointment = Appointment::find($data['id']);


        if (!$appointment) return response()->json(["error" => "appointment does not exist"], 500);
        $appointment->date = $data['date'];
        $appointment->time = $data['time'];
        $appointment->confirmed = 1;

        $updated = $appointment->save();

        if (!$updated) return response()->json(["error" => "something went wrong"], 500);

        $donor = $appointment->donor;

        $donor->notify(new AppointmentAccepted($appointment));

        return response()->json(["error" => false, "updated" => $appointment]);
    }

    public function rescheduleAppointment(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'time' => 'required'
        ]);


        $appointment->date = $data['date'];
        $appointment->time = $data['time'];

        $updated = $appointment->save();

        if (!$updated) return response()->json(["error" => "something went wrong"], 500);


        return response()->json(["error" => false]);
    }

    public function destroy(Appointment $appointment)
    {
        $deleted = $appointment->delete();

        if (!$deleted) return response()->json(["error" => "something went wrong"], 500);
        return response()->json(["error" => false]);
    }
}
