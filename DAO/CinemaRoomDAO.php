<?php

    namespace DAO;

	use \Exception as Exception;
	use DAO\Connection as Connection;
	use Models\CinemaRoom as CinemaRoom;
	use Models\Cinema as Cinema;

    class CinemaRoomDAO {

        private $cinemaRoomList = array();
		private $connection;
		private $tableName = "cinema_rooms";

        public function add(CinemaRoom $cinemaRoom) {
			try {
				$query = "INSERT INTO " . $this->tableName . " (name, capacity, price, FK_id_cinema) VALUES (:name, :capacity, :price, :cinema);";
				$parameters["name"] = $cinemaRoom->getName();
				$parameters["capacity"] = $cinemaRoom->getCapacity();
				$parameters["price"] = $cinemaRoom->getPrice();
				$parameters["cinema"] = $cinemaRoom->getCinema()->getId();
                $this->connection = Connection::getInstance();
				$this->connection->executeNonQuery($query, $parameters);
			}
			catch (Exception $e) {
				throw $e;
			}
        }

        public function getAll() {
			try {								
				$query = "CALL cinemaRooms_GetAll()";
				$this->connection = Connection::GetInstance();
				$results = $this->connection->Execute($query, array(), QueryType::StoredProcedure);
				foreach($results as $row) {
					$cinemaRoom = new CinemaRoom();
					$cinemaRoom->setId($row["cinema_room_id"]);
					$cinemaRoom->setName($row["cinema_room_name"]);
					$cinemaRoom->setCapacity($row["cinema_room_capacity"]);
					$cinemaRoom->setPrice($row["cinema_room_price"]);

					$cinema = new Cinema();
					$cinema->setId($row["cinema_id"]);
					$cinema->setName($row["cinema_name"]);
					$cinema->setAddress($row["cinema_address"]);

					$cinemaRoom->setCinema($cinema);

					array_push ($this->cinemaRoomList, $cinemaRoom);
				}				
				return $this->cinemaRoomList;
			}
			catch(Exception $e) {
				throw $e;
			}
		}

		public function deleteById($id) {
			try {
				$query = "CALL cinemaRooms_deleteById(?)";
				$parameters ["id"] = $id;
				$this->connection = Connection::GetInstance();
				$this->connection->ExecuteNonQuery($query, $parameters, QueryType::StoredProcedure);
			}
			catch (Exception $e) {
				throw $e;
			}
		}

		public function getById($id) {
			try {
				$query = "CALL cinemaRooms_getById(?)";
				$parameters ["id"] = $id;
				$this->connection = Connection::GetInstance();
				$results = $this->connection->Execute($query, $parameters, QueryType::StoredProcedure);
				
				$cinemaRoom = new CinemaRoom();
				foreach($results as $row) {
					$cinemaRoom->setId($row["id"]);
					$cinemaRoom->setName($row["name"]);
					$cinemaRoom->setCapacity($row["capacity"]);
					$cinemaRoom->setPrice($row["price"]);
				}
				return $cinemaRoom;
			}
			catch (Exception $e) {
				throw $e;
			}
		}

		public function checkNameInCinema($name, $id_cinema) {
			try {
				$query = "CALL cinemaRooms_getByNameAndCinema(?, ?)";
				$parameters ["name"] = $name;
				$parameters ["id_cinema"] = $id_cinema;
				$this->connection = Connection::GetInstance();
				$results = $this->connection->ExecuteNonQuery($query, $parameters, QueryType::StoredProcedure);
				
				return $results;
			}
			catch (Exception $e) {
				throw $e;
			}
		}


		public function modify(CinemaRoom $cinemaRoom) {
			try {
				$query = "CALL cinemaRooms_modify(?, ?, ?, ?)";
				$parameters["id"] = $cinemaRoom->getId();
				$parameters["name"] = $cinemaRoom->getName();
				$parameters["capacity"] = $cinemaRoom->getCapacity();
				$parameters["price"] = $cinemaRoom->getPrice();
				$this->connection = Connection::getInstance();
				$this->connection->ExecuteNonQuery($query, $parameters, QueryType::StoredProcedure);
			}
			catch (Exception $e) {
				throw $e;
			}
		}

		public function getByName(CinemaRoom $cinemaRoom) {
			try {								
				$query = "CALL cinemaRooms_getByName(?)";
				$parameters["name"] = $cinemaRoom->getName();
				$this->connection = Connection::GetInstance();
				$results = $this->connection->ExecuteNonQuery($query, $parameters, QueryType::StoredProcedure);				

				return $results;
			}
			catch (Exception $e) {
				throw $e;
			}			
		}

		public function getShowsOfCinema(CinemaRoom $cinemaRoom) {
			try {								
				$query = "CALL cinemaRooms_hasShows(?)";
				$parameters["id"] = $cinemaRoom->getId();
				$this->connection = Connection::GetInstance();
				$results = $this->connection->ExecuteNonQuery($query, $parameters, QueryType::StoredProcedure);				

				return $results;
			}
			catch (Exception $e) {
				throw $e;
			}
		}

    }

 ?>
