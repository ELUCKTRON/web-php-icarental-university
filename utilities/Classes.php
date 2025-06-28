<?php

class Car {
  public $id;
  public $brand;
  public $model;
  public $year;
  public $transmission;
  public $fuel_type;
  public $passengers;
  public $daily_price_huf;
  public $image;

  public function __construct($id, $brand, $model, $year, $transmission, $fuel_type, $passengers, $daily_price_huf, $image) {
      $this->id = $id;
      $this->brand = $brand;
      $this->model = $model;
      $this->year = $year;
      $this->transmission = $transmission;
      $this->fuel_type = $fuel_type;
      $this->passengers = $passengers;
      $this->daily_price_huf = $daily_price_huf;
      $this->image = $image;
  }

}



function createCarsFromArray($data) {
  $cars = [];
  foreach ($data as $carData) {
      $cars[] = new Car(
          $carData['id'],
          $carData['brand'],
          $carData['model'],
          $carData['year'],
          $carData['transmission'],
          $carData['fuel_type'],
          $carData['passengers'],
          $carData['daily_price_huf'],
          $carData['image']
      );
  }
  return $cars;
}


class User {

    public $id;
    public $full_name;
    public $email;
    public $password;
    public $is_admin;
    public $image;

    public function __construct($full_name, $email, $password, $is_admin ,$image,$id) {
        $this->full_name = $full_name;
        $this->email = $email;
        $this->password = $password;
        $this->is_admin = $is_admin;
        $this->image = $image;
        $this->id = $id;
    }

}

class Booking {

  public $id;
  public $start_date;
  public $end_date;
  public $user_email;
  public $car_id;

  public function __construct($id,$start_date, $end_date, $user_email, $car_id) {
      $this->id = $id;
      $this->start_date = $start_date;
      $this->end_date = $end_date;
      $this->user_email = $user_email;
      $this->car_id = $car_id;
  }
}
