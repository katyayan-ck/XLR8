{{-- This file is used to store topbar (right) items --}}


{{-- <li class="nav-item d-md-down-none"><a class="nav-link" href="#"><i class="la la-bell"></i><span
            class="badge badge-pill bg-danger">5</span></a></li>
<li class="nav-item d-md-down-none"><a class="nav-link" href="#"><i class="la la-list"></i></a></li>
<li class="nav-item d-md-down-none"><a class="nav-link" href="#"><i class="la la-map"></i></a></li> --}}
{{-- This file is used to store topbar (right) items - Alerts, Notifications, Messages --}}

<!-- resources/views/vendor/backpack/base/inc/topbar_right_content.blade.php -->
<!-- या theme-tabler/inc/ में अगर वहाँ रख रहे हो -->

<div class="d-flex align-items-center gap-3 me-3">

    <!-- ALERTS -->
    <div class="dropdown">
        <a class="nav-link position-relative text-black p-0" data-bs-toggle="dropdown" href="#" role="button">
            <i class="la la-exclamation-triangle fs-2"></i>
            <span class="position-absolute badge rounded-pill bg-danger alert-badge"
                style="padding: 2px 6px; font-size: 10px; right: 11px; top: 12px; border-radius: 50%;">
                3
            </span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0 dropdown-mobile-style" style="width: 360px;">
            <div class="dropdown-header bg-danger text-black alert-header">Alerts (3 Unread)</div>
            <div class="dropdown-body" style="max-height: 320px; overflow-y: auto;">
                <div
                    class="alert-item dropdown-item d-flex justify-content-between align-items-center py-3 border-bottom">
                    <div>
                        <i class="la la-warning text-danger me-2"></i>
                        <strong class="text-wrap">System maintenance scheduled it is not good</strong><br>
                        <small class="text-muted">2 mins ago</small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary mark-as-read" title="Mark as Read">Read</button>
                </div>
                <div
                    class="alert-item dropdown-item d-flex justify-content-between align-items-center py-3 border-bottom">
                    <div>
                        <i class="la la-warning text-danger me-2"></i>
                        <strong class="text-wrap">High CPU usage detected, it is for testing and production</strong><br>
                        <small class="text-muted">1 hour ago</small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary mark-as-read" title="Mark as Read">Read</button>
                </div>
                <div
                    class="alert-item dropdown-item d-flex justify-content-between align-items-center py-3 top:11px right:14px">
                    <div>
                        <i class="la la-warning text-danger me-2"></i>
                        <strong class="text-wrap">Security update available</strong><br>
                        <small class="text-muted">3 hours ago</small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary mark-as-read" title="Mark as Read">Read</button>
                </div>
            </div>
            <a href="#" class="dropdown-footer text-center py-2">See All Alerts</a>
        </div>
    </div>

    <!-- NOTIFICATIONS -->
    <div class="dropdown">
        <a class="nav-link position-relative text-black p-0" data-bs-toggle="dropdown" href="#" role="button">
            <i class="la la-bell fs-2"></i>
            <span class="position-absolute badge rounded-pill bg-danger notif-badge"
                style="padding: 2px 6px; font-size: 10px; right: 14px; top: 11px; border-radius: 50%;">
                3
            </span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0 dropdown-mobile-style" style="width: 360px;">
            <div class="dropdown-header bg-warning text-black notif-header">Notifications (3 Unread)</div>
            <div class="dropdown-body" style="max-height: 320px; overflow-y: auto;">
                <div
                    class="notif-item dropdown-item d-flex justify-content-between align-items-center py-3 border-bottom">
                    <div>
                        <i class="la la-info-circle text-warning me-2"></i>
                        <strong class="text-wrap">New user registered</strong><br>
                        <small class="text-muted">5 mins ago</small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary mark-as-read" title="Mark as Read">Read</button>
                </div>
                <div
                    class="notif-item dropdown-item d-flex justify-content-between align-items-center py-3 border-bottom ">
                    <div>
                        <i class="la la-clock text-warning me-2"></i>
                        <strong class="text-wrap">Task deadline approaching</strong><br>
                        <small class="text-muted">45 mins ago</small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary mark-as-read" title="Mark as Read">Read</button>
                </div>
                <div class="notif-item dropdown-item d-flex justify-content-between align-items-center py-3">
                    <div>
                        <i class="la la-check-circle text-success me-2"></i>
                        <strong class="text-wrap">System update completed</strong><br>
                        <small class="text-muted">2 hours ago</small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary mark-as-read" title="Mark as Read">Read</button>
                </div>
            </div>
            <a href="#" class="dropdown-footer text-center py-2">See All Notifications</a>
        </div>
    </div>

    <!-- INBOX / MESSAGES -->
    <div class="dropdown">
        <a class="nav-link position-relative text-black p-0" data-bs-toggle="dropdown" href="#" role="button">
            <i class="la la-envelope fs-2"></i>
            <span class="position-absolute badge rounded-pill bg-danger msg-badge"
                style="padding: 2px 6px; font-size: 10px; right: 11px; top: 14px; border-radius: 50%;">
                3
            </span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0 dropdown-mobile-style" style="width: 360px;">
            <div class="dropdown-header bg-success text-black msg-header">Inbox (3 Unread)</div>
            <div class="dropdown-body" style="max-height: 320px; overflow-y: auto;">
                <div
                    class="msg-item dropdown-item d-flex justify-content-between align-items-center py-3 border-bottom">
                    <div>
                        <i class="la la-envelope-open text-success me-2"></i>
                        <strong class="text-wrap">Welcome to the team!</strong><br>
                        <small class="text-muted">10 mins ago</small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary mark-as-read" title="Mark as Read">Read</button>
                </div>
                <div
                    class="msg-item dropdown-item d-flex justify-content-between align-items-center py-3 border-bottom">
                    <div>
                        <i class="la la-calendar text-info me-2"></i>
                        <strong class="text-wrap">Meeting reminder</strong><br>
                        <small class="text-muted">1 hour ago</small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary mark-as-read" title="Mark as Read">Read</button>
                </div>
                <div class="msg-item dropdown-item d-flex justify-content-between align-items-center py-3">
                    <div>
                        <i class="la la-file-alt text-primary me-2"></i>
                        <strong class="text-wrap">Project update</strong><br>
                        <small class="text-muted">4 hours ago</small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary mark-as-read" title="Mark as Read">Read</button>
                </div>
            </div>
            <a href="#" class="dropdown-footer text-center py-2">See All Messages</a>
        </div>
    </div>
</div>

<!-- Mobile adjustments + text wrap -->
<style>
    @media (max-width: 991.98px) {
        .dropdown-mobile-style {
            width: 90vw !important;
            max-width: 340px !important;
            left: 50% !important;
            right: auto !important;
            transform: translateX(-50%) !important;
            margin-top: 12px !important;
            box-sizing: border-box;
        }
    }

    .text-wrap {
        word-break: break-word;
        overflow-wrap: break-word;
        white-space: normal;
    }
</style>

<!-- JavaScript: Mark as Read → item हटाओ + count घटाओ -->
<script>
    document.addEventListener('DOMContentLoaded', function () {

    // सभी mark-as-read buttons पर listener
    document.querySelectorAll('.mark-as-read').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // सबसे नजदीकी dropdown-item ढूंढो
            const item = this.closest('.dropdown-item');
            if (!item) return;

            // उसका dropdown menu
            const dropdown = item.closest('.dropdown-menu');
            if (!dropdown) return;

            // बैज (count वाला span)
            const badge = dropdown.previousElementSibling.querySelector('.badge');
            // हेडर (जैसे "Notifications (3 Unread)")
            const header = dropdown.querySelector('.dropdown-header');

            // item हटाओ
            item.remove();

            // बैज count घटाओ
            if (badge) {
                let count = parseInt(badge.textContent.trim()) || 0;
                count = Math.max(0, count - 1);
                if (count > 0) {
                    badge.textContent = count;
                } else {
                    badge.remove();
                }
            }

            // हेडर में (X Unread) अपडेट करो
            if (header) {
                const currentText = header.textContent;
                const match = currentText.match(/\((\d+)\s*Unread\)/i);
                if (match) {
                    let newCount = parseInt(match[1]) - 1;
                    newCount = Math.max(0, newCount);
                    header.textContent = currentText.replace(match[0], `(${newCount} Unread)`);
                }
            }

            // अगर सारे items खतम हो गए तो empty message दिखाओ
            const remainingItems = dropdown.querySelectorAll('.dropdown-item:not(.dropdown-footer)').length;
            if (remainingItems === 0) {
                const body = dropdown.querySelector('.dropdown-body');
                if (body && !body.querySelector('.empty-message')) {
                    body.innerHTML = '<div class="dropdown-item text-center text-muted py-4 empty-message">No unread items</div>';
                }
            }
        });
    });
});
</script>
