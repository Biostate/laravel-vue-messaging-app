<?php

namespace Database\Factories;

use App\Models\UserContact;
use Illuminate\Database\Eloquent\Factories\Factory;
class UserContactFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserContact::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        do{
            $from = rand(1,15);
            $to = rand(1,15);
        }while($from === $to || UserContact::where('user_id', $from)->where('contact_id', $to)->exists());
        return [
            'user_id' => $from,
            'contact_id' => $to,
        ];
    }
}
