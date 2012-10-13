<div class="strength user_position_{$iUserId}">
    <div class="count
        {if $aRatingInfo['delta_position'] < 0}
            count-remove
        {elseif $aRatingInfo['delta_position'] > 0}
            count-add
        {elseif $aRatingInfo['delta_position'] == 0}
            count-none
        {/if}" id="user_rang_{$iUserId}">{$aRatingInfo['new_position']}<span>{if $aRatingInfo['delta_position'] > 0}
        +{$aRatingInfo['delta_position']}
        {elseif $aRatingInfo['delta_position'] < 0}
        {$aRatingInfo['delta_position']}
    {/if}</span></div>
    <div class="vote-label">{$aLang.plugin.opinion.rang}</div>
</div>