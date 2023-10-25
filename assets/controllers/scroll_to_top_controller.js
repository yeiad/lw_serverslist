import $ from 'jquery';
import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    scroll(event) {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }

    connect() {
        $(window).scroll(
            function () {
                var initialOpacity = 0;
                var maxOpacity = 1;
                var scaling = 0.9;
                var scrollTop = $(this).scrollTop();
                var elementHeight = $(this).height();
                var opacity =
                    (maxOpacity - (elementHeight - scrollTop) / elementHeight) * scaling
                    + initialOpacity;

                if (opacity <= 1.1) {
                    $('.scrolling').css({
                        opacity: function () {
                            return opacity;
                        }
                    });
                }
            }
        );

    }
}