<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ChatUpdatesIndicator extends Component
{
    public $lastUpdate;
    public $isPolling = true;
    
    public function mount()
    {
        $this->lastUpdate = now()->format('H:i:s');
    }
    
    public function render()
    {
        $this->lastUpdate = now()->format('H:i:s');
        return view('livewire.chat-updates-indicator');
    }
    
    public function togglePolling()
    {
        $this->isPolling = !$this->isPolling;
        $this->emit($this->isPolling ? 'resumePolling' : 'stopPolling');
    }
}
