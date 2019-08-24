<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use Proexe\BookingApp\Bookings\Models\BookingModel;
use Proexe\BookingApp\Utilities\ResponseTimeCalculator;

class CalculateResponseTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookingApp:calculateResponseTime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculates response time';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle() {
        $responseTimeCalculator = new ResponseTimeCalculator();
        $bookings = BookingModel::with('office')->get()->toArray();

        $format = "Y-m-d H:i:s";

        foreach ($bookings  as $book ) {

            $createAt = DateTime::createFromFormat($format, $book['created_at'])->format('D');
            $updatedAt = DateTime::createFromFormat($format, $book['updated_at'])->format('D');

            $this->line( "created_at = {$book['created_at']} ({$createAt})");
            $this->line( "updated_at = {$book['updated_at']} ({$updatedAt})");
            $this->line( "Opening hours:");

            $this->hours($book['office']['office_hours']);

        }
//        $this->line( "Response time will be: {$responseTimeCalculator->calculate($bookingDateTime, $responseDateTime, $officeHours)}" );

	    //Use ResponseTimeCalculator class for all calculations
	    //You can use $this->line() to write out any info to console
    }

    /**
     * @param $data
     * @throws \Exception
     */
    private function hours($data)
    {
        $hours = [];
        foreach ($data as $key => $item) {
            $newBag  = 0;
            if($item['isClosed'] === true) {
                $newBag++;
                $hours[$newBag][$this->getWeekName($key)] = "closed";
            } else {
                $hours[$newBag][$this->getWeekName($key)] = [
                    $this->map($item['from']),
                    $this->map($item['to'])
                ];
            }
        }

        dump($hours);

    }

    /**
     * @param $key
     * @return string
     * @throws \Exception
     */
    private function getWeekName($key)
    {
        switch($key) {
            case 0:
                return 'sunday';
                break;
            case 1:
                return 'monday';
                break;
            case 2:
                return 'tuesday';
                break;
            case 3:
                return 'wednesday';
                break;
            case 4:
                return 'thursday';
                break;
            case 5:
                return 'friday';
                break;
            case 6:
                return 'saturday';
                break;
            default:
                throw new \Exception('Name of week not found');
        }

    }

    private function map($mapper)
    {
        $exp = explode(":", $mapper);
        if(strlen($exp[0]) === 1) {
            return '0' . $mapper;
        }
        return $mapper;
    }
}
