<?php

namespace PDFPro\Base;

if ( ! defined( 'ABSPATH' ) ) exit;

class ProModal {

    public function register() {
        if ( pdfp_fs()->can_use_premium_code() ) {
            return;
        }

        add_action( 'admin_head', [ $this, 'admin_head_styles' ] );
        add_action( 'admin_footer', [ $this, 'admin_footer_html' ] );
        add_action( 'admin_footer', [ $this, 'admin_footer_scripts' ] );
    }

    public function admin_head_styles() {
        ?>
        <style>
            .bplProModalOverlay {
                position: fixed;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                display: none;
                align-items: center;
                justify-content: center;
                background: rgba(0, 0, 0, 0.5) ;
                backdrop-filter: blur(4px);
                z-index: 100000;
            }

            .bplProModalOverlay.active {
                display: flex;
            }

            .bplProModal {
                max-width: 906px;
                width: 95vw;
                padding: 0;
                border: none;
                border-radius: 16px;
                overflow: hidden;
                margin: 0 auto;
                min-height: 400px;
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
                background: #fff;
                position: relative;
                padding: 24px;
            }

            .bplProModal .close {
                width: 32px;
                height: 32px;
                position: absolute;
                top: 24px;
                right: 24px;
                background: none;
                border: none;
                cursor: pointer;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 0;
                border: 1px solid #146EF5;
            }

            .bplProModal .close:hover,
            .bplProModal .close:focus,
            .bplProModal .close:active {
                background: none;
                border: 1px solid #146EF5;
                outline: 0;
                box-shadow: none;
            }

            .bplProModal .close svg {
                width: 32px;
                height: 32px;
                color: #146EF5;
            }

            /* LEFT SECTION */
            .bplProModal .left {
                width: 350px;
                max-width: 100%;
                background: #DAE6F7;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 30px;
                border-radius: 16px;
            }

            .bplProModal .left svg {
                min-width: 120px;
                width: 242px;
                max-width: 100%;
                height: auto;
                fill: #146EF5;
            }

            /* RIGHT SECTION */
            .bplProModal .right {
                flex: 1;
                min-width: 300px;
                width: 100%;
                max-width: 500px;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .bplProModal .right h2 {
                font-size: 24px;
                line-height: 1.33;
                font-weight: 600;
                color: #001737;
                margin: 0 0 16px 0;
                text-align: left;
            }

            .bplProModal .right p {
                font-size: 16px;
                line-height: 1.5;
                color: #1B2E4B;
                margin: 0 0 23px 0;
                text-align: left;
            }

            .bplProModal .right h3 {
                font-size: 18px;
                line-height: 1.33;
                font-weight: 600;
                color: #001737;
                margin: 0 0 8px 0;
                text-align: left;
            }

            .bplProModal .right ul {
                list-style: none;
                padding: 0;
                margin: 0 0 32px 0;
                text-align: left;
            }

            .bplProModal .right ul li {
                display: flex;
                align-items: flex-start;
                gap: 8px;
                margin: 0 0 4px 0;
            }

            .bplProModal .right ul li svg {
                width: 16px;
                height: 15px;
                flex-shrink: 0;
                fill: #146EF5;
                margin-top: .2em;
            }

            .bplProModal .right ul li span {
                font-size: 14px;
                font-weight: 500;
                color: #1B2E4B;
                line-height: 1.4;
            }

            .bPlButton {
                font-size: 14px;
                font-weight: 500;
                text-decoration: none;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 12px 28px;
                border: none;
                border-radius: .5em;
                outline: 0;
                cursor: pointer;
                background: #146EF5;
                color: #fff;
                text-align: center;
            }

            .bPlButton:hover,
            .bPlButton:focus {
                outline: 0;
                color: white;
                box-shadow: none;
            }
        </style>
        <?php
    }

    public function admin_footer_html() {
        ?>
        <div class="bplProModalOverlay" id="bplProModalOverlay">
            <div class="bplProModal" id="bplProModal">
                <!-- LEFT -->
                <div class="left">
                    <div class="illustration">
                        <svg xmlns='http://www.w3.org/2000/svg' fill-rule='evenodd' viewBox='0 0 512 512'>
                            <path d='m320.9 491.64h-266.93c-29.74 0-53.94-24.2-53.94-53.94v-168.46c0-29.74 24.2-53.94 53.94-53.94h266.93c3.78 0 7.52.4 11.18 1.18.06 9.37 1.52 18.77 4.39 27.92l-3.99 6.93c-4.84 8.38-6.13 18.28-3.62 27.63 1.06 3.96 2.74 7.65 4.97 10.96l-66.83 115.75c-2.38 4.11-3.03 9.03-1.81 13.61l9.7 36.55c2.09 7.89 9.24 13.38 17.39 13.38 2.39 0 1.94.06 4.3-.52l32.59-8.01c4.7-1.16 8.74-4.16 11.2-8.32 2.33-3.94 3.06-8.63 2.07-13.07 4.17-1.39 7.69-4.26 9.9-8.08s2.93-8.33 2.04-12.63c4.17-1.38 7.71-4.25 9.92-8.08 2.21-3.82 2.93-8.33 2.04-12.63 3.37-1.12 6.32-3.2 8.5-5.98v45.81c0 29.74-24.2 53.94-53.94 53.94zm-133.46-49.19c-12.97 0-23.52-10.55-23.52-23.51v-64.84c-14.92-8.43-24.37-24.41-24.37-41.72 0-26.4 21.48-47.89 47.89-47.89 26.4 0 47.88 21.48 47.88 47.89 0 17.31-9.45 33.29-24.38 41.72v64.84c0 12.96-10.54 23.51-23.5 23.51zm0-165.95c-19.79 0-35.89 16.09-35.89 35.88 0 13.96 8.2 26.76 20.89 32.61 2.12.98 3.48 3.1 3.48 5.45v68.5c0 6.34 5.17 11.51 11.51 11.51 6.35 0 11.51-5.17 11.51-11.51v-68.5c0-2.35 1.37-4.47 3.49-5.45 12.69-5.85 20.89-18.65 20.89-32.61 0-19.79-16.1-35.88-35.88-35.88z' /><path d='m250.57 203.3v-50.24c0-34.82-28.32-63.14-63.14-63.14-34.81 0-63.13 28.32-63.13 63.14v50.24h-69.57v-50.24c0-73.17 59.53-132.7 132.7-132.7h.01c73.17 0 132.7 59.53 132.7 132.7v50.24z' />
                            <path d='m413.54 322.58c-4.26 0-8.47-1.13-12.16-3.26l-49.57-28.62c-5.64-3.25-9.67-8.52-11.36-14.84s-.84-12.9 2.42-18.53l6.73-11.67c-9.01-23.7-6.96-49.69 5.78-71.75 14.95-25.89 42.83-41.98 72.76-41.98 14.64 0 29.13 3.89 41.88 11.26 40.08 23.14 53.86 74.57 30.72 114.64-12.73 22.06-34.21 36.84-59.25 40.88l-6.73 11.67c-4.35 7.53-12.48 12.2-21.22 12.2zm14.48-60.58c-8.05 0-16.01-2.13-23.02-6.18-22.03-12.72-29.6-40.98-16.88-63.01 8.21-14.23 23.53-23.07 39.98-23.07 8.05 0 16.01 2.14 23.02 6.18 10.67 6.17 18.31 16.11 21.5 28.01 3.18 11.91 1.55 24.34-4.61 35.01-8.22 14.22-23.54 23.06-39.99 23.06zm.08-80.26c-12.17 0-23.51 6.54-29.59 17.07-9.41 16.3-3.81 37.21 12.49 46.62 5.19 2.99 11.07 4.57 17.02 4.57 12.18 0 23.52-6.54 29.6-17.07 9.4-16.29 3.8-37.21-12.5-46.61-5.18-3-11.07-4.58-17.02-4.58z' />
                            <path d='m292.28 457.21c-2.66 0-5.08-1.77-5.79-4.46l-9.7-36.54c-.41-1.54-.2-3.17.6-4.54l64.98-112.56 56.45 32.59-5.64 9.76c-.79 1.38-2.1 2.38-3.64 2.79l-13.94 3.74 2.83 10.54c.41 1.53.19 3.17-.61 4.55-.79 1.38-2.1 2.38-3.64 2.8l-10.54 2.82 2.83 10.54c.41 1.54.19 3.17-.6 4.55-.8 1.38-2.11 2.38-3.65 2.8l-10.54 2.82 2.83 10.54c.41 1.53.2 3.17-.6 4.55s-2.11 2.38-3.64 2.79l-10.54 2.83 2.82 10.54c.41 1.53.2 3.17-.6 4.55-.79 1.38-2.1 2.38-3.64 2.79l-10.54 2.83 2.9 10.82c.42 1.56.19 3.22-.63 4.6-.82 1.39-2.17 2.39-3.73 2.78l-32.6 8.01c-.47.12-.96.17-1.43.17z' />
                        </svg>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="right">
                    <h2 id="proModalTitle">Unlock More with PDF Poster Pro!</h2>

                    <p id="proModalDescription">
                        The free features of PDF Poster do a lot—still, without PRO, you're holding yourself back from getting more.
                    </p>

                    <h3>With PRO, You’ll Get:</h3>

                    <ul id="proFeaturesList">
                        <!-- Features injected by JS -->
                    </ul>

                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=pdf-poster-pricing' ) ); ?>" target="_blank" id="upgradeButton" class="bPlButton">
                        Upgrade Now
                    </a>
                </div>

                <!-- CLOSE BUTTON -->
                <button class="close" id="closeProModal" aria-label="Close modal">
                    <svg stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 15 15" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.7816 4.03157C12.0062 3.80702 12.0062 3.44295 11.7816 3.2184C11.5571 2.99385 11.193 2.99385 10.9685 3.2184L7.50005 6.68682L4.03164 3.2184C3.80708 2.99385 3.44301 2.99385 3.21846 3.2184C2.99391 3.44295 2.99391 3.80702 3.21846 4.03157L6.68688 7.49999L3.21846 10.9684C2.99391 11.193 2.99391 11.557 3.21846 11.7816C3.44301 12.0061 3.80708 12.0061 4.03164 11.7816L7.50005 8.31316L10.9685 11.7816C11.193 12.0061 11.5571 12.0061 11.7816 11.7816C12.0062 11.557 12.0062 11.193 11.7816 10.9684L8.31322 7.49999L11.7816 4.03157Z" fill="currentColor"></path></svg>
                </button>

            </div>
        </div>
        <?php
    }

    public function admin_footer_scripts() {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const proModalOverlay = document.getElementById('bplProModalOverlay');
                const closeBtn = document.getElementById('closeProModal');
                const featuresList = document.getElementById('proFeaturesList');
                const upgradeBtn = document.getElementById('upgradeButton');

                const features = [
                   "Industry-Leading Adobe Viewer: Give your audience the absolute best reading experience with the world’s most trusted engine.",
                   "Effortless Cloud Sync: Stop manually uploading—connect directly to your Dropbox or Google Drive and show documents instantly.",
                   "Worry-Free Content Protection: Keep your work safe by easily disabling right-clicks and text copying with a single toggle.",
                   "Stunning Click-to-Open Popups: Swap boring links for beautiful images that open your PDFs in a professional, premium window.",
                   "Distraction-Free 'Reader Mode': Let your content shine by hiding menus and clutter for a clean, immersive reading experience.",
                   "Always Up-to-Date Documents: PRO automatically serves your latest file version, so you never have to manually update a link again.",
                   "Perfect View Every Time: Set exactly which page opens first and at what zoom level, so readers see exactly what you intended.",
                   "Priority One-on-One Support: Skip the queue and get expert help whenever you need it from the team that knows the plugin best."
                ];

                function renderFeatures() {
                    featuresList.innerHTML = '';
                    features.forEach(feature => {
                        const li = document.createElement('li');

                        li.innerHTML = `
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z"></path></svg>
                        <span>${feature}</span>`;
                        featuresList.appendChild(li);
                    });
                }

                function openProModal() {
                    renderFeatures();
                    proModalOverlay.classList.add('active');
                }

                function closeProModal() {
                    proModalOverlay.classList.remove('active');
                }

                document.body.addEventListener('click', function (e) {
                    const proField = e.target.closest('.pdfp-lock-field');
                    if (!proField) return;

                    e.preventDefault();
                    openProModal();
                });

                closeBtn.addEventListener('click', closeProModal);

                proModalOverlay.addEventListener('click', function (e) {
                    if (e.target === proModalOverlay) {
                        closeProModal();
                    }
                });

            });
        </script>
        <?php
    }
}
