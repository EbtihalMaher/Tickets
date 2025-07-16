<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Models\Ticket;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\Api\V1\TicketResource;
use App\Policies\V1\TicketPolicy;

class TicketController extends ApiController
{
    protected $policyClass = TicketPolicy::class;

    /**
     * Display a listing of the resource.
     */
    public function index(TicketFilter $filters)
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        $this->authorize('store', Ticket::class);

        return new TicketResource(
            Ticket::create($request->mappedAttributes())
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        if ($this->include('author')) { 
            return new TicketResource($ticket->load('user'));
        }
        return new TicketResource($ticket);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request,Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $ticket->update($request->mappedAttributes());
        return new TicketResource($ticket);
    }

    public function replace(ReplaceTicketRequest $request,Ticket $ticket) {
        $this->authorize('replace', $ticket);

        $ticket->update($request->mappedAttributes());
        return new TicketResource($ticket);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        $this->authorize('destroy', $ticket);

        $ticket->delete();
        return $this->ok('Ticket successfully deleted');
    }
}
