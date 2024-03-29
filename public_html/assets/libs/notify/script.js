var notify_start = 10;
$.notify = function (type) {
    let _this = this;

    let icons = {
        danger: "fa-times",
        success: "fa-check",
        info: "fa-info",
        warning: "fa-exclamation"
    };

    this.organizer = () => {
        start = notify_start;
        $('.custom-notify').each((_, item) => {
            item = $(item);;
            item.css('bottom', `${start}px`);
            start += (20 + item.height());
        });
    };

    this.notiftest = (seconds = 5) => {
        $.notify('danger').show('Consectetur eu eu cillum commodo.', seconds);
        $.notify('success').show('Anim dolor Lorem pariatur in. Aute eiusmod do non irure pariatur eu sunt quis quis do minim. Lorem cupidatat ut esse esse ex exercitation.', seconds);
        $.notify('info').show(`Est nulla exercitation deserunt nostrud elit sunt anim nulla officia eiusmod eu proident laborum irure. Nulla laborum consequat aliquip laborum dolore occaecat velit ut incididunt veniam exercitation consectetur tempor. Anim mollit exercitation Lorem dolor. Id voluptate ipsum nostrud et aute. Qui minim consectetur culpa sit veniam sint proident enim magna commodo irure quis voluptate nostrud. Commodo mollit elit id deserunt id. Ea ea est officia non. Sunt ipsum ut non nostrud duis amet aliquip anim non ad magna. Veniam Lorem nisi aliquip Lorem excepteur. Do exercitation ea enim ex.`, seconds);
        $.notify('warning').show('Anim dolor Lorem pariatur in. Aute eiusmod do non irure pariatur eu sunt quis quis do minim. Lorem cupidatat ut esse esse ex exercitation.', seconds);
    };

    this.clear = () => $('.custom-notify').remove();

    this.remove = is => {
        $(is).fadeOut(200, function () {
            $(this).remove();
            _this.organizer();
        });
    }

    this.show = (text, seconds = 5) => {
        $('body').append(`
            <div class="custom-notify bg-${type}">
                <div class="icon"><i class="fa ${icons[type]}"></i></div>
                <div class="close">&times;</div>
                <div class="text">${text}</div>
            </div>
        `);
        let last_item = $([...$('.custom-notify')].pop());

        setTimeout(() => {
            _this.remove(last_item);
        }, (seconds * 1000));

        last_item.find('.close').on('click', function () {
            _this.remove(last_item);
        });

        _this.organizer();
        return _this;
    };

    return this;
};