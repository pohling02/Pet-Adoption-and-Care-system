<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\AdoptionApplication;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

class User extends Authenticatable {

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory,
        Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $primaryKey = 'UserID';
    protected $fillable = [
        'name', 'email', 'password', 'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function adoptedPets() {
        return $this->hasMany(AdoptionApplication::class, 'AdopterID', 'UserID')
                        ->where('adoption_applications.AdoptionStatus', 'Approved')
                        ->with('pet'); // Assuming AdoptionRequest has a 'pet' relationship
    }

    public function pets() {
        return $this->hasMany(Pet::class, 'OwnerID', 'UserID'); // Adjust column names as per your database
    }

    public function username() {
        return 'email'; // Tell Laravel to use 'UserEmail' as the username field
    }

    public function adoptionApplications() {
        return $this->hasMany(AdoptionApplication::class, 'UserID', 'id');
    }

    // One-to-Many: User has many Messages
    public function messages() {
        return $this->hasMany(Message::class, 'UserID', 'id');
    }

    // One-to-Many: User has many Notifications
    public function notifications() {
        return $this->hasMany(Notification::class, 'UserID', 'UserID');
    }

    // One-to-Many (Optional): User has many Doctors (if doctors are linked to users)
    public function doctors() {
        return $this->hasMany(Doctor::class, 'UserID', 'id');
    }

    // One-to-Many: User has many Appointments (if users book appointments with doctors)
    public function appointments() {
        return $this->hasMany(Appointment::class, 'AdopterID', 'UserID');
    }

    public function scopeAdopters($query) {
        return $query->where('role', 'adopter');
    }

    public function scopeShelterStaff($query) {
        return $query->where('role', 'shelter_staff');
    }

    public function scopeAdmins($query) {
        return $query->where('role', 'admin');
    }

    public function adopterProfile() {
        return $this->hasOne(AdopterProfile::class, 'UserID', 'UserID');
    }

    public function shelterStaffProfile() {
        return $this->hasOne(ShelterStaffProfile::class, 'UserID', 'UserID');
    }

    public function sentMessages() {
        return $this->hasMany(Message::class, 'SenderID');
    }

    public function receivedMessages() {
        return $this->hasMany(Message::class, 'ReceiverID');
    }

    public function sendPasswordResetNotification($token) {
        $url = route('adopter.password.reset', ['token' => $token, 'email' => $this->email]);

        $this->notify(new class($url) extends ResetPasswordNotification {

            public $url;

            public function __construct($url) {
                $this->url = $url;
            }

            public function toMail($notifiable) {
                return (new MailMessage)
                                ->subject('Reset Your Password')
                                ->line('Click the button below to reset your password.')
                                ->action('Reset Password', $this->url)
                                ->line('If you did not request a password reset, ignore this email.');
            }
        });
    }
}
