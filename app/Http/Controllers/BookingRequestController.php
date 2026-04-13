<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequestRequest;
use App\Http\Requests\UpdateBookingRequestRequest;
use App\Models\BookingRequest;

class BookingRequestController extends Controller
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
    public function store(StoreBookingRequestRequest $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(BookingRequest $bookingRequest): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BookingRequest $bookingRequest): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequestRequest $request, BookingRequest $bookingRequest): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BookingRequest $bookingRequest): void
    {
        //
    }
}
