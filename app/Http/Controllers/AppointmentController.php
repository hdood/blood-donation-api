<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Donor;
use App\Models\Question;
use App\Notifications\AppointmentRejected;
use App\States\Cancelled;
use App\States\Pending;
use App\States\PendingToAccepted;
use App\States\Rescheduled;
use App\States\AcceptedToRescheduled;
use App\States\Accepted;
use App\States\ToCancelled;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{

    public function appointmentRequests()
    {

        $appointments = Appointment::where('state', Pending::class)->with('donor')->with(['questions' => function ($query) {
            $query->withPivot('data', 'id');
        }])->get();

        return $appointments;
    }
    public function todayAppointments()
    {
        return Appointment::where(['state' => Accepted::class])->whereDate('date', Carbon::today())->whereTime('time', ">=", Carbon::now())->with('donor')->orderBy("created_at", 'desc')->get();
    }

    public function acceptedAppointments()
    {
        return Appointment::where(['state' => Accepted::class])->orWhere('state', Rescheduled::class)->whereDate('date', '>=', Carbon::today()->format("y-m-d"))->with('donor')->orderBy("date", 'asc')->get();
    }
    public function cancelledAppointments()
    {
        return Appointment::where(['state' => Cancelled::class])->with('donor')->orderBy("date", 'asc')->get();
    }

    public function state()
    {
        $donor =  Auth::guard("donor")->user();

        $appointment = $donor->appointments()->where(["state" => Pending::class])->orWhere(["state" => Accepted::class])->orWhere(["state" => Rescheduled::class])->first();

        if ($appointment) {
            return $appointment->state->getInfo();
        }

        return response()->json(["state" => '']);
    }

    public function book(Request $request)
    {
        $data = $request->validate([
            'donor_id' => 'required',
            'answers' => 'required',
            'time' => 'required',
            'date' => 'required'
        ]);
        $appointment = Appointment::create($data);

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
        ]);
        $appointment = Appointment::find($data['id']);

        if (!$appointment) return response()->json(["error" => "appointment does not exist"], 500);

        $updated = $appointment->state->transition(new PendingToAccepted($appointment));

        if (!$updated) return response()->json(["error" => "something went wrong"], 500);

        return response()->json(["error" => false, "updated" => $appointment]);
    }

    public function rescheduleAppointment(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'time' => 'required'
        ]);


        $updated = $appointment->state->transition(new AcceptedToRescheduled($appointment, $data));

        if (!$updated) return response()->json(["error" => "something went wrong"], 500);


        return response()->json(["error" => false]);
    }

    public function cancel()
    {
        $donor  = Auth::guard('donor')->user();

        $appointment = $donor->appointments()
            ->where(["state" => Pending::class])
            ->orWhere(["state" => Accepted::class])
            ->orWhere(["state" => Rescheduled::class])->first();

        $cancelled = $appointment->state->transition(new ToCancelled($appointment));

        if (!$cancelled) return response()->json(["error" => "something went wrong"], 500);

        return response()->json(["error" => false]);
    }
    public function destroy(Appointment $appointment)
    {

        $donor = $appointment->donor;
        $donor->notify(new AppointmentRejected());

        $deleted = $appointment->delete();

        if (!$deleted) return response()->json(["error" => "something went wrong"], 500);
        return response()->json(["error" => false]);
    }

    public function getFreeHours($date)
    {
        $todayAppointments = Appointment::where(['state' => Accepted::class])->whereDate('date', Carbon::today())->whereTime('time', ">=", Carbon::now())->with('donor')->orderBy("created_at", 'desc')->get();
        $occupiedHours = [];
        $todayAppointments->map(function ($appointment) {
            $occupiedHours[] = $appointment->time;
        });

        $start = Carbon::createFromTime(8, 0, 0);
        $end = Carbon::createFromTime(16, 0, 0);
        $allHours = [];
        for ($hour = $start; $hour->lessThan($end); $hour->addHour()) {
            $allHours[] = $hour->format('H:i');
        }
        $freeHours = array_diff($allHours, $occupiedHours);
        return response()->json($freeHours);
    }
}
