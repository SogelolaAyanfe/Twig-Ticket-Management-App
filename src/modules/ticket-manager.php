<?php
// src/modules/ticket-manager.php

require_once __DIR__ . '/auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class TicketManager {
    
    /**
     * Get all tickets
     * Returns: ['tickets' => array, 'error' => string|null]
     */
    public static function getTickets() {
        // every initially calls is useIsAuthourized from modules/auth; if false return isError: message.
        $isAuthorized = Auth::isAuthorized();
        $tickets = isset($_SESSION['tickets']) ? $_SESSION['tickets'] : [];
        $error = null;
        
        if (!$isAuthorized) {
            $error = "Unauthorized: Cannot retrieve tickets";
            return ['tickets' => [], 'error' => $error];
        }
        
        return ['tickets' => $tickets, 'error' => $error];
    } // return array of tickets
    
    /**
     * Create a new ticket
     * @param string $title
     * @param string $description
     * @param string $status (default: 'OPEN')
     * Returns: ['success' => bool, 'error' => string|null]
     */
    public static function createTicket($title, $description, $status = 'OPEN') {
        // check if user is authorized, if authorized proceed to allow creation
        $isAuthorized = Auth::isAuthorized();
        $tickets = isset($_SESSION['tickets']) ? $_SESSION['tickets'] : [];
        $error = null;
        
        if ($isAuthorized) {
            // Generate unique ID
            $id = 'ticket-' . (int)(microtime(true) * 1000) . '-' . substr(md5(rand()), 0, 9);
            
            $newTicket = [
                'id' => $id,
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'createdAt' => date('Y-m-d H:i:s')
            ];
            
            // store created ticket in session (equivalent to localStorage)
            $tickets[] = $newTicket;
            $_SESSION['tickets'] = $tickets;
            
            return ['success' => true, 'error' => null];
        } else {
            $error = "Ticket can't be created";
            return ['success' => false, 'error' => $error];
        }
    }
    
    /**
     * Get ticket statistics
     * Returns: ['totalTickets' => int, 'openTickets' => int, 'resolvedTickets' => int, 'error' => string|null]
     */
    public static function getTicketStats() {
        $isAuthorized = Auth::isAuthorized();
        $tickets = isset($_SESSION['tickets']) ? $_SESSION['tickets'] : [];
        $error = null;
        
        if (!$isAuthorized) {
            $error = "Unauthorized: Cannot retrieve ticket stats";
            return [
                'totalTickets' => 0,
                'openTickets' => 0,
                'resolvedTickets' => 0,
                'error' => $error
            ];
        }
        
        $totalTickets = count($tickets);
        
        $openTickets = count(array_filter($tickets, function($ticket) {
            return $ticket['status'] === 'OPEN';
        }));
        
        $resolvedTickets = count(array_filter($tickets, function($ticket) {
            return $ticket['status'] === 'CLOSED';
        }));
        
        return [
            'totalTickets' => $totalTickets,
            'openTickets' => $openTickets,
            'resolvedTickets' => $resolvedTickets,
            'error' => $error
        ];
    }
    // return an object of the below
    // Total tickets: number
    // Open tickets: number
    // Resolved tickets: number
    
    /**
     * Update an existing ticket
     * @param string $id - Ticket ID
     * @param string|null $title - New title (optional)
     * @param string|null $description - New description (optional)
     * @param string|null $status - New status (optional)
     * Returns: ['success' => bool, 'error' => string|null]
     */
    // accepts ticket id
    // returns update ticket
    public static function updateTicket($id, $title = null, $description = null, $status = null) {
        $isAuthorized = Auth::isAuthorized();
        $tickets = isset($_SESSION['tickets']) ? $_SESSION['tickets'] : [];
        $error = null;
        
        if ($isAuthorized) {
            $updatedTickets = array_map(function($ticket) use ($id, $title, $description, $status) {
                if ($ticket['id'] === $id) {
                    if ($title !== null) {
                        $ticket['title'] = $title;
                    }
                    if ($description !== null) {
                        $ticket['description'] = $description;
                    }
                    if ($status !== null) {
                        $ticket['status'] = $status;
                    }
                }
                return $ticket;
            }, $tickets);
            
            $_SESSION['tickets'] = $updatedTickets;
            
            return ['success' => true, 'error' => null];
        } else {
            $error = "Ticket can't be updated";
            return ['success' => false, 'error' => $error];
        }
    }
    
    /**
     * Delete a ticket
     * @param string $id - Ticket ID
     * Returns: ['success' => bool, 'error' => string|null]
     */
    public static function deleteTicket($id) {
        $isAuthorized = Auth::isAuthorized();
        $tickets = isset($_SESSION['tickets']) ? $_SESSION['tickets'] : [];
        $error = null;
        
        if ($isAuthorized) {
            $filteredTickets = array_filter($tickets, function($ticket) use ($id) {
                return $ticket['id'] !== $id;
            });
            
            // Re-index array to maintain proper indexing
            $_SESSION['tickets'] = array_values($filteredTickets);
            
            return ['success' => true, 'error' => null];
        } else {
            $error = "Ticket can't be deleted";
            return ['success' => false, 'error' => $error];
        }
    }
}
?>