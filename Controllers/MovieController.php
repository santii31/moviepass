<?php

    namespace Controllers;

    use Models\Movie as Movie;    
    use DAO\MovieDAO as MovieDAO;
    use Controllers\GenreToMovieController as GenreToMovieController;
    use Controllers\ShowController as ShowController;
    use Controllers\PurchaseController as PurchaseController;
    use Controllers\UserController as UserController;   

    class MovieController {

        private $movieDAO;
		private $showController;

        public function __construct() {
            $this->movieDAO = new MovieDAO();
			$this->showController = new ShowController();
        }

        public function moviesNowPlaying() {
            return $this->movieDAO->getAll();
        }

		public function moviesNowPlayingOnShow() {
            return $this->showController->moviesOnShow();
        }

        public function moviesUpcoming() {
            return $this->movieDAO->getComingSoonMovies();
        }

        public function showMovie($id) {                              
            $genreMovieController = new GenreToMovieController();    
            $purchaseController = new PurchaseController();            
            $movieTemp = new Movie();
            $movieTemp->setId($id);

            $movie = $this->movieDAO->getById($movieTemp);

            if ($movie->getTitle() != null) {
                $title = $movie->getTitle();
                $img = IMG_PATH_TMDB . $movie->getBackdropPath();
                $keyTrailer = $this->movieDAO->getKeyMovieTrailer($movie);     
                $shows = $this->showController->getShowsOfMovieById($id);
                $genres = $genreMovieController->getGenresOfMovie($movie); 

                require_once(VIEWS_PATH . "header.php");
                require_once(VIEWS_PATH . "header-s.php");
                require_once(VIEWS_PATH . "navbar.php");
                require_once(VIEWS_PATH . "datasheet.php");
                require_once(VIEWS_PATH . "footer.php");
            } else {
                return $this->nowPlaying();
            }
        }

        // Converts the runtime of a movie in minutes to xhr xm
        private function minToHour($time, $format = '%2dhr %02dm') {
            if ($time < 1) {
                return;
            }
            $hours = floor($time / 60);
            $minutes = ($time % 60);
            return sprintf($format, $hours, $minutes);
        }

		public function nowPlaying($movies = "", $title = "", $alert = "") {            
            $genreController = new GenreToMovieController();            
            $genres = $genreController->getGenresOfMoviesOnShows();            
            $img = IMG_PATH . '/w4.png';
                        
            if ($movies == null && $alert == null) {
                $movies = $this->moviesNowPlayingOnShow();            
            }
            if ($title == null) {
                $title = 'Now Playing';
            }
            
			require_once(VIEWS_PATH . "header.php");
			require_once(VIEWS_PATH . "navbar.php");
			require_once(VIEWS_PATH . "header-s.php");
			require_once(VIEWS_PATH . "now-playing.php");
			require_once(VIEWS_PATH . "footer.php");
		}
        
        // Imagen para cada genero???
        public function filterMovies($id = "", $date = "") {                        
            $genreMovieController = new GenreToMovieController();            
            $genres = $genreMovieController->getAllGenres();            
            $img = IMG_PATH . '/w4.png';   

            if (!empty($id) && empty($date)) {                                
                //Filtramos solo por genero
                $nameGenre = $genreMovieController->getNameOfGenre($id);                   
                $title = 'Now Playing - ' . $nameGenre;         
                $movies = $genreMovieController->searchMoviesOnShowByGenre($id); 

                return (!empty($movies)) ? $this->nowPlaying($movies, $title) : $this->nowPlaying($movies, $title, MOVIES_NULL);

            } else if (!empty($date) && empty($id)) {                                
                //Filtramos solo por fecha                
                $movies = $genreMovieController->searchMoviesOnShowByDate($date);
                $title = 'Now Playing - ' . $date; 

                return (!empty($movies)) ? $this->nowPlaying($movies, $title) : $this->nowPlaying($movies, $title, MOVIES_NULL);

            } else if (!empty($id) && !empty($date)) {                
                //Filtramos por genero y fecha
                $nameGenre = $genreMovieController->getNameOfGenre($id);            
                $title = 'Now Playing - ' . $nameGenre . ' - ' . $date;
                $movies = $genreMovieController->searchMoviesOnShowByGenreAndDate($id, $date);  
                                                       
                return (!empty($movies)) ? $this->nowPlaying($movies, $title) : $this->nowPlaying($movies, $title, MOVIES_NULL);
                
            } else {                                
                return $this->nowPlaying();
            }            
        } 

		public function comingSoon() {
			$title = 'Coming Soon';
            $img = IMG_PATH . '/w5.png';
            $movies = $this->moviesUpcoming();

			require_once(VIEWS_PATH . "header.php");
			require_once(VIEWS_PATH . "navbar.php");
			require_once(VIEWS_PATH . "header-s.php");
			require_once(VIEWS_PATH . "coming-soon.php");
            require_once(VIEWS_PATH . "footer.php");            
		}

		public function getNowPlayingMoviesFromDAO() {
			$this->movieDAO->getNowPlayingMoviesFromDAO();
			$this->movieDAO->getRunTimeMovieFromDAO();
        }

        public function addMoviePath($alert = "", $success = "") {
			if (isset($_SESSION["loggedUser"])) {
                $admin = $_SESSION["loggedUser"];
                if ($admin->getRole() == 1) {
				    require_once(VIEWS_PATH . "admin-head.php");
				    require_once(VIEWS_PATH . "admin-header.php");
                    require_once(VIEWS_PATH . "admin-movie-add.php");
                } else {
                    $userController = new UserController();
                    return $userController->userPath();
                }
			} else {
                $userController = new UserController();
                return $userController->userPath();
            }
        }        
        
        public function add($id) {
            $movie = new Movie();
            $movie->setId($id);                        
            if ($this->movieDAO->existMovie($movie) == null) {         
            // if ($this->movieDAO->getById($movie) == null) {
                $movieDetails = $this->movieDAO->getMovieDetailsById($movie);         
                if ($this->movieDAO->addMovie($movieDetails)) {
                    $genreMovieController = new GenreToMovieController();    
                    if ($genreMovieController->addGenresBD($movieDetails)) {
                        return $this->addMoviePath(null, MOVIE_ADDED);                                    
                    }
                    return $this->addMoviePath(null, MOVIE_ADDED . ' But the genres cant added with success.');     //arreglar
                }
                
            }            
            return $this->addMoviePath(MOVIE_EXIST, null);
        }        

        // borrado logico
        public function remove($id) {
            if ($this->movieHasShows($id)) {
                return $this->listMoviePath(null, MOVIE_HAS_SHOWS, $id);
            } else {
                $movie = new Movie();
                $movie->setId($id);
                $this->movieDAO->deleteById($movie);
                return $this->listMoviePath(MOVIE_REMOVE, null, null);
            }
        }

		public function forceDelete($id) {
            $movie = new Movie();
            $movie->setId($id);
            $this->movieDAO->deleteById($movie);
            
			return $this->listMoviePath(MOVIE_REMOVE, null, null);
		}        
        
        private function movieHasShows($id) {
			$movie = new Movie();
			$movie->setId($id);

			return ($this->movieDAO->getShowsOfMovie($movie)) ? true : false;
		}
        
        public function listMoviePath($success = "", $alert = "", $movieId = "") {
			if (isset($_SESSION["loggedUser"])) {
                $admin = $_SESSION["loggedUser"];
                if ($admin->getRole() == 1) {
                    $movies = $this->movieDAO->getAll();
                    if ($movies) {
                        require_once(VIEWS_PATH . "admin-head.php");
                        require_once(VIEWS_PATH . "admin-header.php");
                        require_once(VIEWS_PATH . "admin-movie-list.php");
                    } else {
                        $userController = new UserController();
                        return $userController->adminPath();
                    }
                }
			} else {
                $userController = new UserController();
                return $userController->userPath();
            }            
        }         

        public function searchMovie($title) {            
            $movieTemp = new Movie();
            $movieTemp->setTitle($title);                       
            $movie = $this->movieDAO->getByTitle($movieTemp);
            if ($movie->getId() == null) {
                return $this->nowPlaying($movie, MOVIES_NULL , MOVIES_NULL);
            } else {
                return $this->showMovie($movie->getId());
            }
        }
    
        public function sales() {
			if (isset($_SESSION["loggedUser"])) {
				$admin = $_SESSION["loggedUser"];
				if ($admin->getRole() == 1) {
                    $movies = $this->moviesNowPlayingOnShow();
                    if ($movies) {                        
                        require_once(VIEWS_PATH . "admin-head.php");
                        require_once(VIEWS_PATH . "admin-header.php");
                        require_once(VIEWS_PATH . "admin-movie-sales.php");
                    } else {
                        $userController = new UserController();
                        return $userController->adminPath();
                    }
				}
			} else {
                $userController = new UserController();
                return $userController->userPath();
            } 
        }
        
        public function getMovieById(Movie $movie) {            
            return $this->movieDAO->getById($movie);
        }

    }

 ?>