<?php

namespace App\Http\Controllers;

use App\Http\Resources\SquadResource;
use App\Models\Squad;
use App\Models\User;
use App\Notifications\SquadSubmitted;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class SquadController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $featured = Squad::featured()->get();

        if ($featured->count() < 9) {
            $not_featured = Squad::where('featured', false)->inRandomOrder()->take(9 - $featured->count())->get();
        }

        $squads = $featured->merge($not_featured);

        return SquadResource::collection($squads);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! $request->has('requires_approval')) {
            $request->merge(['requires_approval' => false]);
        }
        if ($request->has('rank')) {
            $request['rank'] = Str::of($request->rank)->value();
        }
        if (! $request->has('rank')) {
            $request['rank'] = array_key_first(config('uniteagency.squad_ranks'));
        }

        $validated = $request->validate([
            'email' => 'nullable|email|max:255',

            'name' => 'required|string|unique:squads,name|max:15',
            'code' => 'required|string|unique:squads,code|max:9',
            'requires_approval' => 'nullable|boolean',
            'country' => 'nullable|string|size:2',
            'rank' => 'required|string|max:8',
            'active_members' => 'nullable|integer|min:1|max:30',
            'link' => 'nullable|url|max:255',
            'description' => 'nullable|max:300',
        ]);

        if ($validated['email'] ?? false) {
            $user = User::whereEmail($validated['email'])->first();

            if (! $user) {
                $user = $this->userService->store([
                    'email' => $validated['email'],
                ]);
            }
            $validated = array_merge($validated, ['user_id' => $user->id]);
        }

        $squad = Squad::create(collect($validated)->except(['email'])->toArray());

        // Notification::route('slack', config('services.slack.notification_webhook'))
        //     ->notify(new SquadSubmitted($squad));

        return new SquadResource($squad);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
