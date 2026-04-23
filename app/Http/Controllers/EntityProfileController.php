<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntityProfileRequest;
use App\Http\Requests\UpdateEntityProfileRequest;
use App\Models\EntityProfile;

class EntityProfileController extends Controller
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
    public function store(StoreEntityProfileRequest $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EntityProfile $entityProfile): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EntityProfile $entityProfile): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEntityProfileRequest $request, EntityProfile $entityProfile): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EntityProfile $entityProfile): void
    {
        //
    }
}
