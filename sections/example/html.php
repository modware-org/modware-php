<?php
// Get section data from query
$videoId = $sectionData['video_id'] ?? '';
$currentLang = $_GET['lang'] ?? 'en';
?>

<div class="example-section">
    <div class="container">
        <!-- Title with translation -->
        <h2 class="section-title">
            [translate key="example_section_title" lang="<?php echo $currentLang; ?>"]
        </h2>

        <!-- Description with translation -->
        <div class="section-description">
            [translate key="example_section_description" lang="<?php echo $currentLang; ?>"]
        </div>

        <!-- YouTube video using shortcode -->
        <div class="video-wrapper">
            [youtube id="<?php echo htmlspecialchars($videoId); ?>" width="800" height="450" autoplay="0" controls="1"]
        </div>

        <!-- Additional content with translation -->
        <div class="content-blocks">
            <div class="block">
                <h3>[translate key="example_block1_title" lang="<?php echo $currentLang; ?>"]</h3>
                <p>[translate key="example_block1_content" lang="<?php echo $currentLang; ?>"]</p>
            </div>
            <div class="block">
                <h3>[translate key="example_block2_title" lang="<?php echo $currentLang; ?>"]</h3>
                <p>[translate key="example_block2_content" lang="<?php echo $currentLang; ?>"]</p>
            </div>
        </div>
    </div>
</div>
