
<?php
require_once 'VolunteerRoles.php';

class Cook extends VolunteerRoles
{
    public function chooseRole(): void {
        $this->roleType |= self::COOK_FLAG;  // Set the Cook flag
    }
}