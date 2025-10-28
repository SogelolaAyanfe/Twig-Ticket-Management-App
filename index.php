<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/config/session.php';
require_once __DIR__ . '/src/modules/auth.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

$error = null;
$success = null;

switch ($path) {
    case '/':
    case '/home':
        echo $twig->render('home.html.twig', [
            'title' => 'Home - Ticket Management System',
            'current_page' => 'home'
        ]);
        break;
        
    case '/login':
        // Handle login form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            
            if (empty($email) || empty($password)) {
                $error = "Email and password are required";
            } else {
                $result = Auth::login($email, $password);
                
                if ($result['success']) {
                    $success = $result['message'];
                } else {
                    $error = $result['error'];
                }
            }
        }
        
        echo $twig->render('auth/login.html.twig', [
            'title' => 'Login - Ticket Management System',
            'current_page' => 'login',
            'error' => $error,
            'success' => $success
        ]);
        break;
        
    case '/signup':
        // Handle signup form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            
            if (empty($email) || empty($password)) {
                $error = "Email and password are required";
            } else {
                $result = Auth::signUp($email, $password);
                
                if ($result['success']) {
                    $success = "Sign up successful! Redirecting to login...";
                } else {
                    $error = $result['error'];
                }
            }
        }
        
        echo $twig->render('auth/signup.html.twig', [
            'title' => 'Sign Up - Ticket Management System',
            'current_page' => 'signup',
            'error' => $error,
            'success' => $success
        ]);
        break;
        
   case '/dashboard':
    // Check authorization
    if (!Auth::isAuthorized()) {
        header('Location: /login');
        exit;
    }
    
    require_once __DIR__ . '/src/modules/ticket-manager.php';
    
    // Get ticket statistics
    $stats = TicketManager::getTicketStats();
    $totalTickets = $stats['totalTickets'];
    $openTickets = $stats['openTickets'];
    $resolvedTickets = $stats['resolvedTickets'];
    $statsError = $stats['error'];
    
    // Calculate in-progress tickets
    $inProgressTickets = $totalTickets - $openTickets - $resolvedTickets;
    
    echo $twig->render('dashboard.html.twig', [
        'title' => 'Dashboard - Ticket Management System',
        'current_page' => 'dashboard',
        'totalTickets' => $totalTickets,
        'openTickets' => $openTickets,
        'resolvedTickets' => $resolvedTickets,
        'inProgressTickets' => $inProgressTickets,
        'statsError' => $statsError
    ]);
    break;
        
    case '/ticket-manager':
        // Check authorization
        if (!Auth::isAuthorized()) {
            header('Location: /login');
            exit;
        }
        
        require_once __DIR__ . '/src/modules/ticket-manager.php';
        
        $getError = null;
        $updateError = null;
        $deleteError = null;
        $createSuccess = null;
        $createError = null;
        $editingTicket = null;
        
        // Handle POST requests (create, update, delete)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'create':
                    $title = trim($_POST['title'] ?? '');
                    $description = trim($_POST['description'] ?? '');
                    $status = $_POST['status'] ?? 'OPEN';

                    if (empty($title) || empty($description) || empty($status)) {
                          $createError = "All fields are required";
                      }  else {
                        $result = TicketManager::createTicket($title, $description);
                        if ($result['success']) {
                            // Redirect after successful creation
                            header('Location: /ticket-manager?created=1');
                            exit;
                        } else {
                            $createError = $result['error'];
                        }
                    }
                    break;
                    
                case 'update':
                    $ticketId = $_POST['ticket_id'] ?? '';
                    $title = trim($_POST['title'] ?? '');
                    $description = trim($_POST['description'] ?? '');
                    $status = $_POST['status'] ?? '';
                    
                    if (empty($title) || empty($description) || empty($status)) {
                        $updateError = "All fields are required";
                    } else {
                        $result = TicketManager::updateTicket($ticketId, $title, $description, $status);
                        if ($result['success']) {
                            // Redirect to clear POST data
                            header('Location: /ticket-manager?updated=1');
                            exit;
                        } else {
                            $updateError = $result['error'];
                        }
                    }
                    break;
                    
                case 'delete':
                    $ticketId = $_POST['ticket_id'] ?? '';
                    $result = TicketManager::deleteTicket($ticketId);
                    if ($result['success']) {
                        // Redirect to clear POST data
                        header('Location: /ticket-manager?deleted=1');
                        exit;
                    } else {
                        $deleteError = $result['error'];
                    }
                    break;
            }
        }
        
        // Handle GET request for editing
        if (isset($_GET['edit'])) {
            $editingTicket = $_GET['edit'];
        }
        
        // Show success messages from redirects
        if (isset($_GET['created'])) {
            $createSuccess = "Ticket created successfully!";
        }
        if (isset($_GET['updated'])) {
            $createSuccess = "Ticket updated successfully!";
        }
        if (isset($_GET['deleted'])) {
            $createSuccess = "Ticket deleted successfully!";
        }
        
        // Get all tickets
        $ticketsResult = TicketManager::getTickets();
        $tickets = $ticketsResult['tickets'];
        if ($ticketsResult['error']) {
            $getError = $ticketsResult['error'];
        }
        
        echo $twig->render('ticket-manager.html.twig', [
            'title' => 'Ticket Manager - Ticket Management System',
            'current_page' => 'ticket-manager',
            'tickets' => $tickets,
            'getError' => $getError,
            'updateError' => $updateError,
            'deleteError' => $deleteError,
            'createSuccess' => $createSuccess,
            'createError' => $createError,
            'editingTicket' => $editingTicket
        ]);
        break;
        
    case '/logout':
        // Handle logout
        Auth::logout();
        header('Location: /home');
        exit;
        break;
        
    default:
        // 404 - redirect to home
        header("Location: /home");
        exit;
}
?>