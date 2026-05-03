<?php
/* @var \Vehica\Widgets\Car\Single\UserSocialSingleCarWidget $vehicaCurrentWidget */
global $vehicaCurrentWidget;
/* @var \Vehica\Model\User\User $vehicaUser */
$vehicaUser = $vehicaCurrentWidget->getUser();
if (!$vehicaUser) {
    return;
}
?>
<div class="vehica-user-field__social_buttons">
    <?php if ($vehicaUser->hasFacebookProfile()) : ?>
        <div class="vehica-social-icon">
            <a
                    href="<?php echo esc_url($vehicaUser->getFacebookProfile()); ?>"
                    title="<?php esc_attr_e('Facebook', 'vehica'); ?>"
                    target="_blank"
            >
                <i class="fab fa-facebook-f"></i>
            </a>
        </div>
    <?php endif; ?>

    <?php if ($vehicaUser->hasInstagramProfile()) : ?>
        <div class="vehica-social-icon">
            <a
                    href="<?php echo esc_url($vehicaUser->getInstagramProfile()); ?>"
                    title="<?php esc_attr_e('Instagram', 'vehica'); ?>"
                    target="_blank"
            >
                <i class="fab fa-instagram"></i>
            </a>
        </div>
    <?php endif; ?>

    <?php if ($vehicaUser->hasLinkedinProfile()) : ?>
        <div class="vehica-social-icon">
            <a
                    href="<?php echo esc_url($vehicaUser->getLinkedinProfile()); ?>"
                    title="<?php esc_attr_e('LinkedIn', 'vehica'); ?>"
                    target="_blank"
            >
                <i class="fab fa-linkedin"></i>
            </a>
        </div>
    <?php endif; ?>

    <?php if ($vehicaUser->hasTwitterProfile()) : ?>
        <div class="vehica-social-icon">
            <a
                    href="<?php echo esc_url($vehicaUser->getTwitterProfile()); ?>"
                    title="<?php esc_attr_e('Twitter', 'vehica'); ?>"
                    target="_blank"
            >
                <i class="fab fa-twitter"></i>
            </a>
        </div>
    <?php endif; ?>

    <?php if ($vehicaUser->hasTiktokProfile()) : ?>
        <div class="vehica-social-icon">
            <a
                    href="<?php echo esc_url($vehicaUser->getTiktokProfile()); ?>"
                    title="<?php esc_attr_e('TikTok', 'vehica'); ?>"
                    target="_blank"
            >
                <i class="fab fa-tiktok"></i>
            </a>
        </div>
    <?php endif; ?>


    <?php if ($vehicaUser->hasTelegramProfile()) : ?>
        <div class="vehica-social-icon">
            <a
                    href="<?php echo esc_url($vehicaUser->getTelegramProfile()); ?>"
                    title="<?php esc_attr_e('Telegram', 'vehica'); ?>"
                    target="_blank"
            >
                <i class="fab fa-telegram"></i>
            </a>
        </div>
    <?php endif; ?>
</div>