<?php

namespace Drupal\student_feedback\Service;

/**
 *
 */
class MailBuilder {

  /**
   *
   */
  public function build(array &$message, array $params) {

    $message['subject'] = '📩 New Feedback Received';

    $message['headers']['Content-Type'] = 'text/html; charset=UTF-8';

    $message['body'][] = '
    <div style="font-family: Arial, sans-serif; background:#f4f6f9; padding:20px;">
      
      <div style="max-width:600px; margin:auto; background:white; border-radius:10px; padding:20px; border:1px solid #eee;">
        
        <h2 style="margin-top:0; color:#333;">New Feedback Submitted</h2>

        <table style="width:100%; border-collapse: collapse;">
          <tr>
            <td style="padding:8px; font-weight:bold;">Name:</td>
            <td style="padding:8px;">' . $params['name'] . '</td>
          </tr>
          <tr>
            <td style="padding:8px; font-weight:bold;">Email:</td>
            <td style="padding:8px;">' . $params['email'] . '</td>
          </tr>
          <tr>
            <td style="padding:8px; font-weight:bold;">Message:</td>
            <td style="padding:8px;">' . $params['message'] . '</td>
          </tr>
          <tr>
            <td style="padding:8px; font-weight:bold;">Rating:</td>
            <td style="padding:8px;">
              <span style="background:#f59e0b; color:white; padding:4px 8px; border-radius:5px;">
                ⭐ ' . $params['rating'] . '/5
              </span>
            </td>
          </tr>
        </table>

        <hr style="margin:20px 0;">

        <p style="font-size:12px; color:#999;">
          Student Feedback System
        </p>

      </div>

    </div>
  ';
  }

}
