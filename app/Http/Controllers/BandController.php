<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreBandRequest;
use App\Http\Requests\UpdateBandRequest;
use App\Models\Band;

class BandController extends Controller
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
    public function store(StoreBandRequest $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Band $band): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Band $band): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBandRequest $request, Band $band): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Band $band): void
    {
        //
    }
}
