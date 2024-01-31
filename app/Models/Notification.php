<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use function Symfony\Component\String\s;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'read_at',
        'tsr_sender_id',
    ];
    //Accepted means TSR is accepted by admin
    const ACCEPTED_TSR = 'accepted';

    //Rejected means TSR is rejected by admin
    const REJECTED_TSR = 'rejected';

    //Pending means TSR is not accepted nor rejected
    const PENDING_TSR = null;

    //Processed means TSR was accepted and is finished(processed)
    const PROCESSED_TSR = 'processed';

    //Terminated means TSR was accepted but was interrupted(terminated)
    const TERMINATED_TSR = 'terminated';

    //Skipped TSR means TSR was handled by another admin
    const SKIPPED_TSR = 'skipped';

    /**
     * Returns users that should get notification
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'notifiable_id');
    }

    /**
     * Returns user that sent TSR
     */
    public function senderOfTSR()
    {
        return $this->belongsTo(User::class, 'tsr_sender_id');
    }

    /**
     * Make recipients for notifications, merging users that should get notification and admins, but also removes duplicates
     */
    public static function finalRecipientsForNotifications($userIds)
    {
        $userIds = gettype($userIds) == "array" ? $userIds : [$userIds];
        $admins = User::admins()->get();
        $users = User::whereIn("id", $userIds)->get();

        return collect()->merge($users)->merge($admins)->unique();
    }

    public function scopeNotifications($query)
    {
        return $query;
    }

    public function scopeUnreadNotifications($query)
    {
        return $query->where('read_at', null);
    }

    /**
     * Pending Technical Support Requests
     * @return void
     */
    public function scopePendingTSRs($query)
    {
        return $query->where('tsr_status', self::PENDING_TSR);
    }

    /**
     * Accepted Technical Support Requests
     */
    public function scopeAcceptedTSRs($query)
    {
        return $query->where('tsr_status', self::ACCEPTED_TSR);
    }

    /**
     * Rejected Technical Support Requests
     */
    public function scopeRejectedTSRs($query)
    {
        return $query->where('tsr_status', self::REJECTED_TSR);
    }

    /**
     * Processed Technical Support Requests
     */
    public function scopeProcessedTSRs($query)
    {
        return $query->where('tsr_status', self::PROCESSED_TSR);
    }

    /**
     * Processed Technical Support Requests
     */
    public function scopeTerminatedTSRs($query)
    {
        return $query->where('tsr_status', self::TERMINATED_TSR);
    }


    /**
     * Retrieve all technical support requests
     */
    public function scopeTechnicalSupportRequests($query)
    {
        return $query->notifications()->where('type', 'App\Notifications\TechnicalSupportRequest');
    }

    /**
     * Retrieve all unread technical support requests
     */
    public function scopePendingTechnicalSupportRequests($query)
    {
        return $query->technicalSupportRequests()->pendingTSRs();
    }

    /**
     * Retrieve all technical support requests from user
     */
    public function scopeTechnicalSupportRequestsByUser($query, $user)
    {
        return $query->technicalSupportRequests()->where('tsr_sender_id', $user->id);
    }

    /**
     * Retrieve all technical support requests from user
     */
    public function scopePendingTechnicalSupportRequestsByUser($query, $user)
    {
        return $query->technicalSupportRequests()->where('tsr_sender_id', $user->id)->pendingTSRs();
    }

    /**
     * Accept TSRs
     */
    public function scopeAcceptTSRsFromUser($query, $user)
    {
        $query->pendingTechnicalSupportRequestsByUser($user)->where('notifiable_id', Auth::user()->id)->update([
            'read_at' => Carbon::now(),
            'tsr_status' => self::ACCEPTED_TSR
        ]);
    }

    /**
     * Rejects TSRs
     */
    public function scopeRejectTSRsFromUser($query, $user)
    {
        return $query->pendingTechnicalSupportRequestsByUser($user)->where('notifiable_id', Auth::user()->id)->update([
            'read_at' => Carbon::now(),
            'tsr_status' => self::REJECTED_TSR
        ]);
    }

    /**
     * Terminates TSRs
     */
    public function scopeTerminateTSRsFromUser($query, $user)
    {
        $query->technicalSupportRequestsByUser($user)->where('notifiable_id', Auth::user()->id)->acceptedTSRs()->update([
            'read_at' => Carbon::now(),
            'tsr_status' => self::TERMINATED_TSR
        ]);
    }

    /**
     * Mark TSRs as processed
     */
    public function scopeProcessTSRsFromUser($query, $user)
    {
        return $query->technicalSupportRequests($user)->where('notifiable_id', Auth::user()->id)->acceptedTSRs()->update([
            'read_at' => Carbon::now(),
            'tsr_status' => self::PROCESSED_TSR
        ]);
    }

    /**
     * Mark TSRs as skipped(handled by another admin)
     */
    public function scopeSkipTSRsFromUser($query, $user)
    {
        return $query->pendingTechnicalSupportRequests($user)->where('notifiable_id', '!=', Auth::user()->id)->update([
            'read_at' => Carbon::now(),
            'tsr_status' => self::SKIPPED_TSR
        ]);
    }


}
