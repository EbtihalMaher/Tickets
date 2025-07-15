<?php

namespace App\Policies\V1;

use App\Models\Ticket;
use App\Models\User;
use App\Permissions\V1\Abilities;

class TicketPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        
    }

    public function store(User $user)
    {
        return $user->tokenCan(Abilities::CreateOwnTicket) || $user->tokenCan(Abilities::CreateTicket);
    }

    public function replace(User $user, Ticket $ticket)
    {
        return $user->tokenCan(Abilities::ReplaceTicket);
    }

    public function update(User $user, Ticket $ticket)
    {
        if ($user->tokenCan(Abilities::UpdateTicket)) {
            return true;
        } else if ($user->tokenCan(Abilities::UpdateOwnTicket)) {
            return $user->id === $ticket->user_id;
        } else {
            return false;
        }   
    }

    public function destroy(User $user, Ticket $ticket)
    {
        if ($user->tokenCan(Abilities::DeleteTicket)) {
            return true;
        } else if ($user->tokenCan(Abilities::DeleteOwnTicket)) {
            return $user->id === $ticket->user_id;
        } else {
            return false;
        }
    }
}
