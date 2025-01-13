<?php

interface State {
    public function handle(Delivery $delivery): void;
}

class PendingState implements State {
    public function handle(Delivery $delivery): void {
        // Handle pending state logic
        $delivery->transitionTo(new DeliveringState());
        $delivery->updateDelivery(['status' => 'delivering']);
    }
}

class DeliveringState implements State {
    public function handle(Delivery $delivery): void {
        // Handle delivering state logic
        $delivery->transitionTo(new DeliveredState());
        $delivery->updateDelivery(['status' => 'delivered']);
    }
}

class DeliveredState implements State {
    public function handle(Delivery $delivery): void {
        // Final state - throw exception if trying to handle
        throw new Exception("Delivery is already in final state");
    }
}