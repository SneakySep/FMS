<?php

namespace App\NewDash;

/**
 * RATINGS  —  ratings.php
 * ---------------------------------------------------------------------------
 * The score summary, the 5..1 distribution bars and the recent-comment list,
 * plus the quick "Rate a delivery" action. js/dashboard.js folds
 * the localStorage 'newdash_ratings' entries on top of the PHP-rendered seed
 * (inlined as #ratingSeed by PageLayout), so a submitted star moves the average
 * and the bars immediately, with no reload and no server.
 *
 * The "Rated / not yet rated" split at the bottom reuses the delivery row
 * partial, so the same rating widgets appear here and in the deliveries table.
 */
class RatingsModule extends DashboardModule
{
    protected string $key      = 'ratings';
    protected string $title    = 'Ratings & Reviews';
    protected string $subtitle = 'What recipients said about recent handovers';
    protected string $template = 'ratings';
    protected array  $css      = [];
    protected array  $js       = [];
    protected bool   $needsRatingModal = true;

    protected function prepare(): void
    {
        $deliveries = DemoData::deliveries();

        $this->vars = [
            'rating_average'   => DemoData::ratingAverage(),
            'rating_total'     => DemoData::ratingTotal(),
            'rating_breakdown' => DemoData::ratingBreakdown(),
            'rating_seed'      => DemoData::ratingSeed(),
            /* Two lists: jobs already scored, and delivered-but-unscored ones
               the desk can still chase feedback for. */
            'reviewed'   => array_values(array_filter($deliveries, fn($d) => $d['rating'] !== null)),
            'awaiting'   => array_values(array_filter(
                $deliveries,
                fn($d) => $d['rating'] === null && $d['status'] === 'delivered'
            )),
            /* views/ratings.php renders its own summary; the lists below reuse
               partials/delivery_row.php, which reads the snake_case keys, and
               the quick-actions partial, which reads kpis / displayName. */
            'status_badges' => DemoData::statusBadges(),
            'vehicle_meta'  => DemoData::vehicleMeta(),
            'kpis'          => DemoData::kpis(),
            'displayName'   => DemoData::DISPLAY_NAME,
        ];
    }

    public function headerSearch(): array|bool|null
    {
        return false;
    }
}
