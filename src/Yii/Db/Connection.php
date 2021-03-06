<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2019 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Yii2tech\Illuminate\Yii\Db;

use Illuminate\Database\Connection as LaravelConnection;
use Illuminate\Support\Facades\DB;

/**
 * Connection allows usage of the Laravel DB connection for Yii one.
 *
 * This class allows sharing of the PDO instance between Laravel and Yii DB connections.
 * It allows establishing of the DB connection only once throughout the entire project.
 * Also it is crucial for running queries in transactions, allowing running Laravel and
 * Yii DB queries in the same transaction.
 *
 * Application configuration example:
 *
 * ```php
 * return [
 *     'components' => [
 *         'db' => Yii2tech\Illuminate\Yii\Db\Connection::class,
 *         // ...
 *     ],
 *     // ...
 * ];
 * ```
 *
 * @see \Illuminate\Database\Connection
 *
 * @property \Illuminate\Database\Connection $illuminateConnection related Laravel DB connection.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class Connection extends \yii\db\Connection
{
    /**
     * @var \Illuminate\Database\Connection Laravel DB connection instance.
     */
    private $_illuminateConnection;

    /**
     * {@inheritdoc}
     */
    public function open(): void
    {
        if ($this->pdo !== null) {
            return;
        }

        $this->pdo = $this->getIlluminateConnection()->getPdo();
    }

    /**
     * {@inheritdoc}
     */
    public function close(): void
    {
        if ($this->pdo === null) {
            return;
        }

        $this->getIlluminateConnection()->disconnect();

        $this->pdo = null;
    }

    /**
     * @param  LaravelConnection  $connection Laravel DB connection to be used.
     * @return static self reference.
     */
    public function setIlluminateConnection(LaravelConnection $connection): self
    {
        $this->_illuminateConnection = $connection;

        return $this;
    }

    /**
     * Returns Laravel DB connection instance.
     *
     * @return \Illuminate\Database\Connection connection instance.
     */
    public function getIlluminateConnection(): LaravelConnection
    {
        if ($this->_illuminateConnection === null) {
            $this->_illuminateConnection = $this->defaultIlluminateConnection();
        }

        return $this->_illuminateConnection;
    }

    /**
     * Defines default Laravel connection.
     *
     * @return LaravelConnection Laravel connection instance.
     */
    protected function defaultIlluminateConnection(): LaravelConnection
    {
        return DB::connection();
    }
}
