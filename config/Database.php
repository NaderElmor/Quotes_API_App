<?php
class Database 
{

    private $hostname = "localhost";

    private $dbname = "quotes_api";

    private $username = "root";

    private $pass = "";

    private $pdo;

     public function __construct()
     {
        $this->pdo = null;

        try
        {
            $this->pdo = new PDO("mysql:host=$this->hostname;dbname=$this->dbname;",$this->username,$this->pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e)
        {
            echo "Error : " . $e->getMessage();
        }
     
    }


    //Start Database Functions
    public function fetchAll($query)
    {
        $stmt =$this->pdo->prepare($query);
        $stmt->execute();

        $rowCount = $stmt->rowCount();

        
        if($rowCount <= 0) 
        { 
            //No records
            return 0; 
        }else 
        { 
            return $stmt->fetchAll();
        }
    }



    public function fetchOne($query, $param)
    {

        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$param]);
        $rowCount = $stmt->rowCount();

        if($rowCount <= 0) 
        { 
            //No records
            return 0; 
        }else 
        { 
            return $stmt->fetch();
        }
    }

    public function insertOne($query, $body, $user_id, $category_id, $date)
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$body, $user_id, $category_id, $date]);
    }

    public function updateOne($query, $body, $user_id, $category_id)
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$body, $user_id, $category_id,]);
    }

    public function deleteOne($query, $id)
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$id]);
    }

    public function inserUser($query, $firstName, $lastName, $Pass, $username)
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$firstName, $lastName, $Pass, $username]);
    }
 






    //keep track user calls and his plans
    public function executeCall($username, $calls_allowed, $timeOutSeconds)
    {
        //#1 get this user
        $query = " SELECT plan, calls_made, time_start, time_end 
                   FROM users 
                   WHERE  username = '$username'";

        $stmt= $this->pdo->prepare($query);
        $stmt->execute([$username]);
        $results = $stmt->fetch();


       //#2 claculate the time
       $userTime = date(time()) - $results['time_start'];

        //If it is timeout set this variable to true
        $timeOut = $userTime >= $timeOutSeconds || $results['time_start'] === 0;

        $query = " UPDATE users SET calls_made = ";
        $query .= $timeOut ? " 1, time_start=".date(time()).", time_end = ". strtotime("+ $timeOutSeconds seconds") : " calls_made + 1";
        $query .= " WHERE username = ?"; 


        //Instead of fetching again I will select all the update variables  
        $results['calls_made'] = $timeOut ? 1 : $results['calls_made'] +1;
        $results['time_end'] = $timeOut ? strtotime("+ $timeOutSeconds seconds") : $results['time_end'];


        // Execute code with respect to plans
        if($results['plan'] == "unlimited"){

            $stmt = $this->pdo->prepare($query);

            $stmt->execute([$username]);

            return $results;

        }else {

            //if no time out and calls made is > calls allowed return -1 
            if($timeOut == false && $results['calls_made'] >= $call_allowed){
                return -1;
            }else{
                //Grant access
                $stmt = $this->pdo->prepare($query);

                $stmt->execute(['username']);

                return $results;
            }
        }
    }
}