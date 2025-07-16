<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\Api\V1\TicketResource;
use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;
 
class AuthorTicketsController extends ApiController
{
    protected $policyClass = TicketPolicy ::class;

    public function index($author_id, TicketFilter $filters) {
        return TicketResource::collection(Ticket::where('user_id', $author_id)->filter($filters)->paginate());
    }

     public function store(StoreTicketRequest $request)
    {
        $this->authorize('store', Ticket::class);

        return new TicketResource(
            Ticket::create($request->mappedAttributes([
                'author' => 'user_id'
            ]))
        );
    }

    public function update(UpdateTicketRequest $request, $author_id, Ticket $ticket) {
        if ($ticket->user_id != $author_id) {
            return $this->error('Unauthorized - Ticket does not belong to author.', 403);
        }

        $this->authorize('update', $ticket);

        $ticket->update($request->mappedAttributes());
        return new TicketResource($ticket);
    }

    public function replace(ReplaceTicketRequest $request, $author_id, Ticket $ticket) {
        if ($ticket->user_id != $author_id) {
            return $this->error('Unauthorized - Ticket does not belong to author.', 403);
        }
        $this->authorize('replace', $ticket);

        $ticket->update($request->mappedAttributes());
        return new TicketResource($ticket);
    }

    public function destroy($author_id, Ticket $ticket)
    {
        if ($ticket->user_id != $author_id) {
            return $this->error('You are not authorized to delete this ticket', 403);
        }
        $this->authorize('destroy', $ticket);

        $ticket->delete();

        return $this->ok('Ticket successfully deleted');
    }
    
}
