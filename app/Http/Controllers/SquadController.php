<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Squad;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\SquadResource;

class SquadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $squads = Squad::paginate();
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'nullable|email|max:255',

            'squad_name' => 'required|string|unique:squads,name|max:255',
            'code' => 'required|string|unique:squads,code|max:255',
            'requires_approval' => 'nullable|boolean',
            'country' => 'nullable|string|size:2',
            'rank' => 'nullable|string|max:8',
            'active_members' => 'nullable|integer|min:1|max:30',
            'description' => 'nullable|max:300',
        ]);

        if($validated['email'] ?? false)
        {
            $random_password = Hash::make(Str::random(12));
            $user = User::firstOrCreate(
                ['email' => $validated['email'] ],
                [
                    'name' => explode('@', $validated['email'])[0],
                    'password' => $random_password,
                ]
            );
            $request->merge(['user_id' => $user->id]);
        }

        $squad = Squad::create( $request->except(['email']) );

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
     * @param  \Illuminate\Http\Request  $request
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
