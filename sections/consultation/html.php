<?php
require_once __DIR__ . '/query.php';
$consultationData = getConsultationData();
?>

<section class="consultation-section" aria-labelledby="consultation-heading">
    <div class="consultation-container">
        <h2 id="consultation-heading">[translate key="consultation_section_title" lang="en"]</h2>
        
        <?php if ($consultationData): ?>
            <div class="consultation-grid">
                <div class="consultation-info">
                    <?php if (isset($consultationData['description'])): ?>
                        <div class="consultation-description">
                            <?php echo htmlspecialchars($consultationData['description']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($consultationData['types']) && !empty($consultationData['types'])): ?>
                        <div class="consultation-types">
                            <h3>[translate key="consultation_types_title" lang="en"]</h3>
                            <div class="types-grid">
                                <?php foreach ($consultationData['types'] as $type): ?>
                                    <div class="consultation-type">
                                        <h4><?php echo htmlspecialchars($type['name']); ?></h4>
                                        <?php if (isset($type['duration'])): ?>
                                            <p class="duration">
                                                <span class="icon">⏱</span>
                                                <?php echo htmlspecialchars($type['duration']); ?> [translate key="minutes" lang="en"]
                                            </p>
                                        <?php endif; ?>
                                        <?php if (isset($type['price'])): ?>
                                            <p class="price">
                                                <span class="icon">💰</span>
                                                <?php echo htmlspecialchars($type['price']); ?> [translate key="currency" lang="en"]
                                            </p>
                                        <?php endif; ?>
                                        <?php if (isset($type['description'])): ?>
                                            <p class="type-description">
                                                <?php echo htmlspecialchars($type['description']); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="consultation-booking">
                    <div class="booking-card">
                        <h3>[translate key="book_consultation_title" lang="en"]</h3>
                        <?php if (isset($consultationData['booking_info'])): ?>
                            <p><?php echo htmlspecialchars($consultationData['booking_info']); ?></p>
                        <?php endif; ?>
                        
                        <?php if (isset($consultationData['contact_methods']) && !empty($consultationData['contact_methods'])): ?>
                            <div class="contact-methods">
                                <?php foreach ($consultationData['contact_methods'] as $method): ?>
                                    <a href="<?php echo htmlspecialchars($method['link']); ?>" 
                                       class="contact-button"
                                       target="_blank"
                                       rel="noopener noreferrer">
                                        <?php echo htmlspecialchars($method['name']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p class="no-consultation">[translate key="consultation_not_available" lang="en"]</p>
        <?php endif; ?>
    </div>
</section>
