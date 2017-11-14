<?php
/**
 * Phanbook : Delightfully simple forum software
 *
 * Licensed under The GNU License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link    http://phanbook.com Phanbook Project
 * @since   1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 */
namespace Phanbook\Badges\Badge;

use Phanbook\Models\Users;
use Phanbook\Models\Categories;
use Phanbook\Models\PostsVotes;
use Phanbook\Models\PostsRepliesVotes;
use Phanbook\Badges\BadgeBase;

/**
 * Phanbook\Badges\Badge\SelfLearner
 * @todo  later
 * Awarded to one that answer his own question
 */
class SelfLearner extends BadgeBase
{
    protected $name = 'Self-Learner';

    protected $description = 'Asked a question and accepted his/her own answer';

    /**
     * Check whether the user can have the badge
     *
     * @param  Users $user
     * @return boolean
     */
    public function canHave(Users $user)
    {
    }
}
