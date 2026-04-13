<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;

class RoleController extends Controller
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
    public function store(StoreRoleRequest $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): void
    {
        //
    }
}
