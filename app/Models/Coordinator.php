<?php

class Coordinator extends VolunteerRoles
{
    
    public function chooseRole(): bool
    {
        $this->roleType |= self::COORDINATOR_FLAG;  // Set Coordinator role flag
        return true;
    }

}
