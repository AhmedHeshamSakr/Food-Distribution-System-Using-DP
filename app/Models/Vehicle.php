<?php

class Vehicle {
    private string $licensePlateNo;
    private int $vehicleID;

    public function __construct(string $licensePlateNo, int $vehicleID)
     {
        $this->licensePlateNo = $licensePlateNo;
        $this->vehicleID = $vehicleID;
        $this->insertVehicle($licensePlateNo);
    }

    public function insertVehicle(String $licensePlateNo): bool
    {
        // Sanitize inputs to prevent SQL injection (if not already done)
        $licensePlateNo = mysqli_real_escape_string(Database::getInstance()->getConnection(), $licensePlateNo);
        

        // SQL query to insert the person into the database
        $query = "INSERT INTO vehicle (licensePlateNo) 
                VALUES ('{$licensePlateNo}')";

        // Run the query and return whether it was successful
        $result = run_query($query);
        
        if ($result) {
            $this->vehicleID = mysqli_insert_id(Database::getInstance()->getConnection());
            return true;
    }
        return false;

    }

    public function updateVehicle(string $licensePlateNo): bool
{
    // Escape the value to prevent SQL injection
    $licensePlateNo = mysqli_real_escape_string(Database::getInstance()->getConnection(), $licensePlateNo);

    // Construct the full SQL query for the Vehicle table
    $query = "UPDATE vehicle SET licensePlateNo = '$licensePlateNo' WHERE vehicleID = '{$this->vehicleID}'";

    // Run the query and return whether it was successful
    return run_query($query);
}

public function deleteVehicle():bool
{
    $query = "DELETE FROM vehicle WHERE vehicleID = '{$this->vehicleID}'";
        return run_query($query);
}

public function getVehicleID():int
{
    return $this->vehicleID;
}
public function getVehiclePlateNo(): int
{
    return $this->licensePlateNo;
}

public function setLicensePlateNo(int $licensePlateNo): bool
{
    $this->licensePlateNo = $licensePlateNo;
    return $this->updateVehicle($licensePlateNo);
}

}