<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;
use App\Models\Borrowing;
use Carbon\Carbon;

class LibraryStatsCommand extends Command
{

    protected $signature = 'library:stats {--period=month}';
    protected $description = 'Affiche les statistiques de la bibliothèque';



    /**
     * Execute the console command.
     */
    public function handle()
    {
        $period = $this->option('period');

        $dateLimit = $period === 'month' ? Carbon::now()->subMonth() : Carbon::now()->subYear();

        $totalBooks = Book::count();
        $totalBorrowings = Borrowing::where('borrowed_at', '>=', $dateLimit)->count();

        $this->info("=== STATISTIQUES DE LA BIBLIOTHÈQUE ===");
        $this->info("Période : " . ucfirst($period));

        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Total Livres', $totalBooks],
                ['Emprunts récents', $totalBorrowings],
            ]
        );
    }

}
