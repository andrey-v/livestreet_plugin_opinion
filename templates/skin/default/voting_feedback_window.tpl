<div class="modal modal-voting-feedback" id="window_voting_feedback_form" style="width: 360px;">
    <header class="modal-header">
        <a href="#" class="close jqmClose"></a>
    </header>

    <h2 style="margin: 0 24px; font-weight: bold; font-size: 16px;">{$aLang.plugin.opinion.comment_vote}</h2>

    <script type="text/javascript">
        jQuery(function($) {
            $('#popup-voting-feedback-form').bind('submit', function() {
                ls.user.voting_feedback('popup-voting-feedback-form');
                return false;
            });
            $('#submit-feedback-text').attr('disabled', false);
        });
    </script>

    <div class="modal-content" style="padding: 12px 24px;">
        <div class="tab-content js-block-popup-voting-feedback-content" data-type="voting-feedback">

            <form action="" method="post" id="popup-voting-feedback-form">
                <label for="voting-feedback-text" style="font-style: italic;">{$aLang.plugin.opinion.feedback}</label>
                <textarea name="voting-feedback-text"
                          id="voting-feedback-text"
                          class="input-text input-width-full"
                          rows="8"
                          placeholder="{$aLang.plugin.opinion.feedback_placeholder}"
                          style="margin-bottom: 12px;"></textarea>
                <input type="hidden" name="voting-id" id="voting-id" value="">
                <input type="hidden" name="voting-type" id="voting-type" value="">
                <button type="submit"
                        name="submit-feedback-text"
                        id="submit-feedback-text"
                        class="button button-primary"
                        style="float: right; margin-bottom: 16px;"
                        disabled="disabled">{$aLang.plugin.opinion.submit}</button>
                <button class="button jqmClose">{$aLang.plugin.opinion.cancel}</button>
            </form>

        </div>
    </div>
</div>