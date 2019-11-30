<?php

    namespace Controllers;    

    use Models\Show as Show;
    use Models\Ticket as Ticket;
    use DAO\TicketDAO as TicketDAO;
    use Controllers\ShowController as ShowController;
    use Controllers\UserController as UserController; 
    use Controllers\ViewsRouterController as ViewsRouter;     

    class TicketController extends ViewsRouter {
        
        private $ticketDAO;
        
        public function __construct() {            
            $this->ticketDAO = new TicketDAO();            
        }

        public function add($qr, $id_show, $id_purchase) {
            $ticket = new Ticket();            
            $ticket->setQr($qr);
            $ticket->setIdPurchase($id_purchase);

            $show = new Show();
            $show->setId($id_show);
            $ticket->setShow($show);
            
            return $this->ticketDAO->add($ticket);
        }

        /* al pedo?
        private function isFormNotEmpty($id_purchase, $id_show) {
            if (empty($id_purchase) || empty($id_show)) {
                return false;
            }
            return true;
        } */
 
        public function getByNumber($number) {
            return $this->ticketDAO-getByNumber($number);
        }

        public function getByShow($id) {
            return  $this->ticketDAO->getByShowId($id);
        }

        public function getTickets() {
            return $this->ticketDAO->getAll();
        }

        public function ticketsNumber($id) {
            $tickets = $this->getByShow($id);
            return count($tickets);
        }
        
        public function ticketsSoldPath() {
			if (isset($_SESSION["loggedUser"])) {
				$admin = $_SESSION["loggedUser"];
				if ($admin->getRole() == 1) {                                         
                    $tickets = $this->ticketDAO->getInfoShowTickets();    
                    if ($tickets) {
                        require_once(VIEWS_PATH . "admin-head.php");
                        require_once(VIEWS_PATH . "admin-header.php");
                        require_once(VIEWS_PATH . "admin-tickets-sold.php");
                    } else {
                        return $this->goToAdminPath();
                    }
                } else {
                    // $userController = new UserController();
                    // return $userController->userPath();
                    return $this->goToUserPath();
                }
			} else {
                // $userController = new UserController();
                // return $userController->userPath();
                return $this->goToUserPath();
            }
        }
        
        public function getTicketsSold($id_show) {
            $show = new Show();
            $show->setId($id_show);
            return $this->ticketDAO->getTicketsOfShows($show);
        }
        
        public function getTickesRemainder($id_show) {
            $cinemaRoomController = new CinemaRoomController();
            
            $show = new Show();
            $show->setId($id_show);
            $cinemaRoom = $cinemaRoomController->getCinemaRoomByShowId($id_show);
            $tickesSold = $this->getTicketsSold($id_show);
            
            return ($cinemaRoom->getCapacity() - $tickesSold);
        }      

    }
?>