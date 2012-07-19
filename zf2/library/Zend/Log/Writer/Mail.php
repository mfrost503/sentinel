<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Log\Writer;

use Zend\Log\Formatter\Simple as SimpleFormatter;
use Zend\Log\Exception;
use Zend\Mail\Message as MailMessage;
use Zend\Mail\Transport;
use Zend\Mail\Transport\Exception as TransportException;

/**
 * Class used for writing log messages to email via Zend\Mail.
 *
 * Allows for emailing log messages at and above a certain level via a
 * Zend\Mail\Message object.  Note that this class only sends the email upon
 * completion, so any log entries accumulated are sent in a single email.
 * The email is sent using a Zend\Mail\Transport\TransportInterface object
 * (Sendmail is default).
 * 
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Mail extends AbstractWriter
{
    /**
     * Array of formatted events to include in message body.
     *
     * @var array
     */
    protected $eventsToMail = array();

    /**
     * Mail message instance to use
     *
     * @var MailMessage
     */
    protected $mail;

    /**
     * Mail transport instance to use; optional.
     *
     * @var Transport\TransportInterface
     */
    protected $transport;

    /**
     * Array keeping track of the number of entries per priority level.
     *
     * @var array
     */
    protected $numEntriesPerPriority = array();

    /**
     * Subject prepend text.
     *
     * Can only be used of the Zend\Mail object has not already had its
     * subject line set.  Using this will cause the subject to have the entry
     * counts per-priority level appended to it.
     *
     * @var string|null
     */
    protected $subjectPrependText;

    /**
     * Constructor
     * 
     * @param MailMessage $mail
     * @param Transport\TransportInterface $transport Optional
     * @return Mail
     */
    public function __construct(MailMessage $mail, Transport\TransportInterface $transport = null)
    {
        $this->mail = $mail;
        if (null !== $transport) {
            $this->setTransport($transport);
        } else {
            // default transport
            $this->setTransport(new Transport\Sendmail());
        }
        $this->formatter = new SimpleFormatter();
    }

    /**
     * Set the transport message
     *
     * @param  Transport\TransportInterface $transport
     * @return Mail
     */
    public function setTransport(Transport\TransportInterface $transport)
    {
        $this->transport = $transport;
        return $this;
    }

    /**
     * Places event line into array of lines to be used as message body.
     *
     * @param array $event Event data
     */
    protected function doWrite(array $event)
    {
        // Track the number of entries per priority level.
        if (!isset($this->numEntriesPerPriority[$event['priorityName']])) {
            $this->numEntriesPerPriority[$event['priorityName']] = 1;
        } else {
            $this->numEntriesPerPriority[$event['priorityName']]++;
        }

        // All plaintext events are to use the standard formatter.
        $this->eventsToMail[] = $this->formatter->format($event);
    }

    /**
     * Allows caller to have the mail subject dynamically set to contain the
     * entry counts per-priority level.
     *
     * Sets the text for use in the subject, with entry counts per-priority
     * level appended to the end.  Since a Zend\Mail\Message subject can only be set
     * once, this method cannot be used if the Zend\Mail\Message object already has a
     * subject set.
     *
     * @param  string $subject Subject prepend text
     * @return Mail
     */
    public function setSubjectPrependText($subject)
    {
        $this->subjectPrependText = (string) $subject;
        return $this;
    }

    /**
     * Sends mail to recipient(s) if log entries are present.  Note that both
     * plaintext and HTML portions of email are handled here.
     */
    public function shutdown()
    {
        // If there are events to mail, use them as message body.  Otherwise,
        // there is no mail to be sent.
        if (empty($this->eventsToMail)) {
            return;
        }

        if ($this->subjectPrependText !== null) {
            // Tack on the summary of entries per-priority to the subject
            // line and set it on the Zend\Mail object.
            $numEntries = $this->getFormattedNumEntriesPerPriority();
            $this->mail->setSubject("{$this->subjectPrependText} ({$numEntries})");
        }

        // Always provide events to mail as plaintext.
        $this->mail->setBody(implode('', $this->eventsToMail));

        // Finally, send the mail.  If an exception occurs, convert it into a
        // warning-level message so we can avoid an exception thrown without a
        // stack frame.
        try {
            $this->transport->send($this->mail);
        } catch (TransportException\ExceptionInterface $e) {
            trigger_error(
                "unable to send log entries via email; " .
                    "message = {$e->getMessage()}; " .
                    "code = {$e->getCode()}; " .
                        "exception class = " . get_class($e),
                E_USER_WARNING);
        }
    }

    /**
     * Gets a string of number of entries per-priority level that occurred, or
     * an empty string if none occurred.
     *
     * @return string
     */
    protected function getFormattedNumEntriesPerPriority()
    {
        $strings = array();

        foreach ($this->numEntriesPerPriority as $priority => $numEntries) {
            $strings[] = "{$priority}={$numEntries}";
        }

        return implode(', ', $strings);
    }
}
