<!-- Latest Users Widget begins -->
<script>
    $().ready(function() {
        $('.widget-latest-users li').each(function () {
            var thisUserName = $('span', this).text();
            var thisImgSrc = $('img', this).attr('src');
            var tooltipTemp = $('.widget-tip-template').clone(true, true);
            
            $('.user-name', tooltipTemp).text(thisUserName);
            $('.avatar-big', tooltipTemp).attr('src', thisImgSrc);

            $('img', this).tooltip({
                placement: 'top',
                html: true,
                trigger: 'manual',
                title: tooltipTemp.html()
            });
        });

        var hoverUsersTimeout;
        $('.widget-latest-users li').hover(function () {
            if (!$(this).find('.tooltip').length){
                $activeQL = $(this);
                clearTimeout(hoverUsersTimeout);
                hoverUsersTimeout = setTimeout(function() {
                    $activeQL.find('img').tooltip('show');
                }, 500);
            }
        }, function () {
            $('.widget-latest-users li').find('img').tooltip('hide');
            clearTimeout(hoverUsersTimeout);
        });
    });
</script>
<div class="widget-holder">
<div class="widget-area widget-latest-users">
    <!-- USER TIP TEMPLATE -->
    <div class='widget-tip-template'>
        <div class='avatar-section'>
            <img class='avatar-big' src='images/photon/user2.jpg' alt='profile' />
        </div>
        <div class='text-section'>
            <span class='user-name'>Prasent Neque</span>
            <span class='user-location'>Paris, France</span>
            <span class='user-info'>nunc.cenenetis@gmail.com<br/>Registred: 9/26/2012 (8:56PM)</span>
        </div>
    </div>

    <div class="widget-head">
        Latest Users
        <div>
            <img src="images/photon/w_latest@2x.png" alt="latest users"/>
        </div>
    </div>
    <ul>
        <li>
            <div class="avatar-image">
                <img src="images/photon/user1.jpg" alt="profile"/>
            </div>
            <span>Vestibulum Odio</span> 
            <div>5 mins</div>
        </li>
        <li>
            <div class="avatar-image">
                <img src="images/photon/user2.jpg" alt="profile"/>
            </div>
            <span>Prasent Neque</span> 
            <div>17 mins</div>
        </li>
        <li>
            <div class="avatar-image">
                <img src="images/photon/user3.jpg" alt="profile"/>
            </div>
            <span>Nunc Cenenatis</span> 
            <div>25 mins</div>
        </li>
        <li>
            <div class="avatar-image">
                <img src="images/photon/user4.jpg" alt="profile"/>
            </div>
            <span>Etiam Libero</span> 
            <div>2 hrs</div>
        </li>
        <li>
            <div class="avatar-image">
                <img src="images/photon/user5.jpg" alt="profile"/>
            </div>
            <span>Morbi Consequat</span> 
            <div>4 hrs</div>
        </li>
    </ul>
</div>
</div>
<!-- Latest Users Widget ends -->
