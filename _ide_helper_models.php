<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models\Buzzer{
/**
 * 
 *
 * @property int $id
 * @property int $lobby_id
 * @property int $BuzzerLocked
 * @property string|null $BuzzedPlayerId
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Buzzer\BuzzerPlayer> $buzzerPlayers
 * @property-read int|null $buzzer_players_count
 * @property-read \App\Models\Lobby\Lobby $lobby
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuzzerLobby newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuzzerLobby newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuzzerLobby query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuzzerLobby whereBuzzedPlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuzzerLobby whereBuzzerLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuzzerLobby whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuzzerLobby whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuzzerLobby whereLobbyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuzzerLobby whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBuzzerLobby {}
}

namespace App\Models\Buzzer{
/**
 * 
 *
 * @property int $id
 * @property string $UserId
 * @property int $buzzer_lobby_id
 * @property int $Points
 * @property int $TextLocked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Buzzer\BuzzerLobby $buzzerLobby
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuzzerPlayer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuzzerPlayer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuzzerPlayer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuzzerPlayer whereBuzzerLobbyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuzzerPlayer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuzzerPlayer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuzzerPlayer wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuzzerPlayer whereTextLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuzzerPlayer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuzzerPlayer whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBuzzerPlayer {}
}

namespace App\Models\Lobby{
/**
 * 
 *
 * @property int $id
 * @property string $HostId
 * @property string $LobbyCode
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Buzzer\BuzzerLobby|null $buzzerLobbies
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lobby newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lobby newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lobby query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lobby whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lobby whereHostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lobby whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lobby whereLobbyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lobby whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLobby {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

