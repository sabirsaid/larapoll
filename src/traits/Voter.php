<?php


namespace Inani\Larapoll\Traits;


use Inani\Larapoll\Exceptions\PollNotSelectedToVoteException;
use Inani\Larapoll\Option;
use Inani\Larapoll\Poll;

trait Voter
{
    protected $poll;

    /**
     * Select poll
     *
     * @param Poll $poll
     * @return $this
     */
    public function poll(Poll $poll)
    {
        $this->poll = $poll;
        return $this;
    }

    /**
     * Vote for an option
     *
     * @param $options
     * @return bool
     * @throws PollNotSelectedToVoteException
     * @throws \Exception
     */
    public function vote($options)
    {
       $options = is_array($options)? $options : func_get_args();
        // if poll not selected
        if(is_null($this->poll))
            throw new PollNotSelectedToVoteException();

        if($this->hasVoted())
            throw new \Exception("User can not vote again!");

        // if is Radio and voted for many options
        $countVotes = count($options);
        if($this->poll->isRadio() && $countVotes > 1)
            throw new \InvalidArgumentException("The poll can not accept many votes option");

        if($this->poll->isCheckable() &&  $countVotes > $this->poll->maxCheck)
            throw new \InvalidArgumentException("selected more options {$countVotes} than the limited {$this->poll->maxCheck}");

        $this->options()->attach($options);
        return true;
    }

    /**
     * Check if he can vote
     *
     * @return bool
     */
    public function hasVoted()
    {
        return false;
    }

    /**
     * The options he voted to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function options()
    {
        return $this->belongsToMany(Option::class, 'votes')->withTimestamps();
    }
}