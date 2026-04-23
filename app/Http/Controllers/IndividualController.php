<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreIndividualRequest;
use App\Http\Requests\UpdateIndividualRequest;
use App\Models\Individual;

class IndividualController extends Controller
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
    public function store(StoreIndividualRequest $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Individual $individual): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Individual $individual): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIndividualRequest $request, Individual $individual): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Individual $individual): void
    {
        //
    }
}
