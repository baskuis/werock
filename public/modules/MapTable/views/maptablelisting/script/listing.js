;(function($, window, document, undefined){
    $().ready(function(){

        /**
         * Add scrollbar
         */
        if(typeof $.fn.mCustomScrollbar !== 'undefined') {
            if($('.listings-wrapper').length > 0) {
                $('.listings-wrapper').mCustomScrollbar({
                    theme: "minimal-dark",
                    scrollInertia: 60
                });
            }
        }

        /**
         * Handle selected class
         */
        $('.listing li a').click(function(){
            $('.listing li.selected').removeClass('selected');
            $(this).parents('li').addClass('selected');
        });

        /**
         * Set reference of original value for
         * compressable spans
         */
        $('.listing li span.compressable').each(function(){
            $(this).attr('data-original-value', $(this).text());
            var currentTitle = $(this).attr('title');
            if(!currentTitle){
                $(this).attr('title', $(this).text());
            }
        });

        /**
         * Resize timeout holder
         * @type {null}
         */
        var truncateStringTimeout = null;

        /**
         * Glue string with these characters
         * or append string with these characters
         * @type {string}
         */
        var glueCharacters = '...';

        /**
         * Truncate/Compress string to fit
         * in allotted parent width
         */
        var truncateString = function(){
            var containerWidth = $('.listing li').width();
            $('.listing li span.compressable').each(function(){

                /**
                 * Calculate the allotted character
                 * @type {number}
                 */
                var allottedCharacters = containerWidth / 15;
                var fontSizeRaw = $(this).css('font-size');
                if(fontSizeRaw){
                    var fontSize = fontSizeRaw.substr(0, fontSizeRaw.length - 2);
                    allottedCharacters = containerWidth / ((fontSize - 0 + 3) * 0.55);
                }

                /**
                 * Get current text from element
                 * @type {*}
                 */
                var data = $(this).data();
                var currentText = (typeof data.originalValue !== 'undefined') ? data.originalValue : $(this).text();

                /**
                 * If we want to compress this string
                 * this will result in prepend .. append
                 */
                if($(this).hasClass('compress')){

                    /**
                     * Modify the displayed string
                     * @type {*}
                     */
                    if(currentText.length > allottedCharacters){
                        var startString = currentText.substr(0, Math.floor(allottedCharacters / 2));
                        var endString = currentText.substr(-(Math.floor(allottedCharacters / 2) - glueCharacters.length));
                        $(this).text(startString + glueCharacters + endString);
                    }else{
                        $(this).text(currentText);
                    }

                }

                /**
                 * If we want to trancate this string
                 * this will result in prepend ..
                 */
                if($(this).hasClass('truncate')){

                    /**
                     * Modify the displayed string
                     * @type {*}
                     */
                    if(currentText.length > allottedCharacters){
                        var newString = currentText.substr(0, allottedCharacters - glueCharacters.length);
                        $(this).text(newString + glueCharacters);
                    }else{
                        $(this).text(currentText);
                    }

                }

                /**
                 * Now ok to show
                 */
                $(this).css('visibility', 'visible');

            });
        }

        /**
         * Truncate/Compress string on load
         */
        truncateString();

        /**
         * When window resizes
         * redetermine how much can be shown
         */
        $(window).resize(function(){
            clearTimeout(truncateStringTimeout);
            truncateStringTimeout = setTimeout(truncateString, 500);
        });

    });
})(jQuery, window, document);