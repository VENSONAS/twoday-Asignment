<?php 
class Charity {
    private static $nextId = 1;
    private $id;
    private $name;
    private $email;
    private $donations;
   
    function __construct($newName, $newEmail) {
        $this->id = self::$nextId++;
        $this->name = $newName;
        $this->email = $newEmail;
        $this->donations = [];
    }

    function setDonation(Donation $new) {
        array_push($this->donations, $new);
    }

    function getDonation() {
        return $this->donations;
    }

    function getId() {
        return $this->id;
    }
    
    function editCharity($newName, $newEmail) {
        $this->name = $newName;
        $this->email = $newEmail;
    }

    function getCharityInfo() {
        echo "\n";
        echo "ID: " . $this->id . "\n";
        echo "Name: " . $this->name . "\n";
        echo "Email: " . $this->email . "\n";
        echo "Donations:\n";
        if(count($this->getDonation())==0) {
            echo "    No donations available.\n";
        }
        else {
        foreach($this->donations as $donation) {
            $donation->getDonationInfo();
        }
    }
        
        
    }
}

class Donation {
    private static $nextId = 1;
    private $id;
    private $donorName;
    private $amount;
    private $charityId;
    private $dateTime;

    function __construct($newDonorName, $newAmount, $charId) {
        $this->id = self::$nextId++;
        $this->donorName = $newDonorName;
        $this->amount = $newAmount;
        $this->charityId = $charId;
        $this->dateTime = date("l jS \of F Y h:i:s A");
    }

    function getDonationInfo() {
        echo "\n";
        echo "    Donor name: " . $this->donorName . "\n";
        echo "    Donation amount: " . $this->amount . "\n";
        echo "    Donation time: " . $this->dateTime . "\n";
    }
}

$allCharities = [];

function uploadCharity(&$allCharities) {
    $allValidEmails = false;
    $inputFile = readline("Enter name(without '.csv' extention) of wanted CSV file: ");
    if (file_exists($inputFile . ".csv")) {
        $file = fopen($inputFile . ".csv", "r");

        while (($currentItems = fgetcsv($file)) !== false) {
            $charityName = $currentItems[0];
            $charityEmail = $currentItems[1];
    
            if (preg_match("/^[^@]+@[^@]+\.[^@]+$/", $charityEmail) == 1) {
                $newChar = new Charity($charityName, $charityEmail);
                $allCharities[] = $newChar;
                $allValidEmails = true;

                echo "\n";
            } else {
                echo "Invalid email at charity: '" . $charityName . "'. Charity not uploaded.\n";
                
            }
        }

    } else {
        echo "\nThe file does not exist.\n";
    }
    if($allValidEmails == true) {
        echo "\nCharity(ies) uploaded successfuly.\n";
    }
}

function createCharity(&$allCharities) {
    echo "\n";
    $charityName = readline("Enter new charity name: ");

    $validEmail = false;
        while($validEmail == false) {
            $charityEmail = readline("Enter new charity email: ");
            if (preg_match("/^[^@]+@[^@]+\.[^@]+$/", $charityEmail) == 1) {
                $newChar = new Charity($charityName, $charityEmail);
                $allCharities[] = $newChar;
                $validEmail = true;
                 echo "\nCharity added successfuly\n";
            }
             else{
                echo "Invalid email, ";
             }
        }
   
    }

function editCharity(&$allCharities) {
    viewCharities($allCharities);
    echo "\n";
    $whichCharity = readline("Which charity to edit? ");
    $charityName = readline("Enter new charity name: ");


    $validEmail = false;
        while($validEmail == false) {
            $charityEmail = readline("Enter new charity email: ");
            if (preg_match("/^[^@]+@[^@]+\.[^@]+$/", $charityEmail) == 1) {
                $validEmail = true;

                foreach ($allCharities as $charity) {
                    if ($charity->getId() == $whichCharity) {
                        $charity->editCharity($charityName, $charityEmail);
                        echo "\nCharity edited successfuly.\n";
                        break;
                    }
                }
            } else {
                echo "Invalid email, ";
            }
        }



    
    
}

function createDonation(&$allCharities) {
    echo "\n";
    viewCharities($allCharities);
    echo "\n";
    
    $whichCharity = readline("Which charity to add donation to? ");
    

    $charityFound = false;
    foreach ($allCharities as $charity) {
        if ($charity->getId() == $whichCharity) {
            $charityFound = true;
            break;
        }
    }

    if (!$charityFound) {
        echo "Invalid charity ID. Please enter a valid ID.\n";
        return;
    }

    $donor = readline("Enter donor name: ");
    $validDonation = false;
    while (!$validDonation) {
    $amountDonated = readline("Enter amount to donate: ");
    echo "\n";

    if (is_numeric($amountDonated)) {
        $newDonation = new Donation($donor, $amountDonated, $whichCharity);

        foreach ($allCharities as $charity) {
            if ($charity->getId() == $whichCharity) {
                $charity->setDonation($newDonation);
                echo "Donation added successfully.\n";
                $validDonation = true;
                break;
                
            }
        }
    } else {
        echo "Please enter a valid number for the donation amount.\n";
    }
}
}

function viewCharities($allCharities) {
    if (count($allCharities)==0) {
        echo "\nNo charities available.\n";
    }
    foreach($allCharities as $charity){
    echo $charity->getCharityInfo() . "\n";
    }
}

function deleteCharity(&$allCharities) {
    $whichCharity = readline("Select which charity to delete (enter ID): ");
    
    foreach ($allCharities as $index => $charity) {
        if ($charity->getId() == $whichCharity) {
            unset($allCharities[$index]);
            echo "\nCharity with ID $whichCharity has been deleted.\n\n";
            return;
        }
    }

    echo "Charity with ID $whichCharity not found.\n";
}

function option(&$allCharities) {
    echo "\nEnter option: \n
     1: View all charities \n
     2: Add a new charity \n
     3: Edit an existing charity's details \n
     4: Delete a charity \n
     5: Add a donation \n
     6: Upload charity file \n
     0: END PROGRAM \n
     Enter your option: ";
    $answer = readline();
    

    switch ($answer) {
        case 1:
            viewCharities($allCharities);
            break;
        case 2:
            createCharity($allCharities);
            break;
        case 3:
            editCharity($allCharities);
            break;
        case 4:
            deleteCharity($allCharities);
            break;
        case 5:
            createDonation($allCharities);
            break;
        case 6:
            uploadCharity($allCharities);
            break;
        case 0:
            echo "\n";
            return;
        default:
            echo "Wrong option, try again:\n";
    }

    option($allCharities);
}

option($allCharities);
?>
