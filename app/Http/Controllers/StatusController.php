<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreStatusRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Models\Status;

class StatusController extends Controller
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
    public function store(StoreStatusRequest $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Status $status): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Status $status): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStatusRequest $request, Status $status): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Status $status): void
    {
        //
    }
}
