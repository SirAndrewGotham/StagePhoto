<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreTheaterRequest;
use App\Http\Requests\UpdateTheaterRequest;
use App\Models\Theater;

class TheaterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): void
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): void
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTheaterRequest $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Theater $theater): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Theater $theater): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTheaterRequest $request, Theater $theater): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Theater $theater): void
    {
        //
    }
}
