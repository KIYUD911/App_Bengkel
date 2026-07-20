<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.guest')]
class LoginForm extends Component
{
    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required|min:6')]
    public string $password = '';

    public bool $remember = false;

    public string $errorMessage = '';

    public function login(): void
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            $this->redirect(route('dashboard'), navigate: true);
            return;
        }

        $this->errorMessage = 'Email atau password yang Anda masukkan salah.';
        $this->password = '';
    }

    public function render()
    {
        return view('livewire.auth.login-form');
    }
}
