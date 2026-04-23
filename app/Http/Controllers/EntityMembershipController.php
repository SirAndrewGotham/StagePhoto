<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntityMembershipRequest;
use App\Http\Requests\UpdateEntityMembershipRequest;
use App\Models\EntityMembership;

class EntityMembershipController extends Controller
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
    public function store(StoreEntityMembershipRequest $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EntityMembership $entityMembership): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EntityMembership $entityMembership): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEntityMembershipRequest $request, EntityMembership $entityMembership): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EntityMembership $entityMembership): void
    {
        //
    }
}
