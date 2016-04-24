<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>

<div>
	<h1>RSS to Events Calendar Importer</h1>
		<p class="description"> Auto-Import Events to the Events Calendar from an RSS feed. New events will be checked for daily as long as Auto-Import is checked. To import just once, uncheck Auto-Import after import is completed. </p>
        <form method="post" action="options.php">
            <?php settings_fields('rssevents_options'); ?>
            <?php $options = get_option('rte_saved'); ?>
            <table class="form-table">
                <tr valign="top"><th scope="row">Auto-Import</th>
                    <td><input name="rte_saved[autoimport]" type="checkbox" value="1" <?php if (isset($options['autoimport'])) { checked('1', $options['autoimport']); } ?> /></td>
                </tr>
                <tr valign="top"><th scope="row">RSS Feed URL</th>
                    <td><input type="text" name="rte_saved[url]" value="<?php echo $options['url']; ?>" />
					<p class="description">The URL for the RSS feed. For example, http://www.experiencela.com/rss/feeds/xlaevents.aspx?id=whatshot</p>
					</td>
				</tr>
			</tbody></table>
			
			<h3>RSS Feed Information</h3>
			<p class="description">Use these fields to inform the plugin from which XML tags to pull event information. Do not input angle brackets.</p>
			<table class="form-table">
				<tbody>
                <tr valign="top"><th scope="row">Event Title Tag</th>
                    <td><input type="text" name="rte_saved[title]" value="<?php echo $options['title']; ?>" />
                    <p class="description">Required. The tag that surrounds the title of the event. For example, put title if the event title is between &lt;title&gt; tags or event if between &lt;event&gt; tags.</p>
					</td>
                </tr>
                <tr valign="top"><th scope="row">Event Description Tag</th>
                    <td><input type="text" name="rte_saved[description]" value="<?php echo $options['description']; ?>" />
                    <p class="description">The tag that surrounds the main description of or most important information about the event.</p>
					</td>
                </tr>
                <tr valign="top"><th scope="row">Optional Additional Description Tag</th>
                    <td><input type="text" name="rte_saved[optdescript]" value="<?php echo $options['optdescript']; ?>" />
                    <p class="description">Optional tag that surrounds other information you want to appear in the body of the event. Will be added to the end of the event description. </p>
					</td>
                </tr>
                <tr valign="top"><th scope="row">Optional Event Description Label</th>
                    <td><input type="text" name="rte_saved[optlabel]" value="<?php echo $options['optlabel']; ?>" />
                    <p class="description">Optional label to put in for clarity between the text found in the description and that found in the additional description field.  This is not a tag, this field will be displayed exactly as is in each event's description. For example, Cost information: or Region:</p>
					</td>
                </tr>
                <tr valign="top"><th scope="row">Start Date and Time Tag</th>
                    <td><input type="text" name="rte_saved[stdatetime]" value="<?php echo $options['stdatetime']; ?>" />
                    <p class="description">Required. The tag that surrounds the start date and time information. Expects to find date/time string in a valid format for strtotime(), for example Sun, 24 Apr 2016 11:00:00 GMT -07:00 or 2016-04-19 018:23:42</p>
					</td>
                </tr>
                <tr valign="top"><th scope="row">End Date and Time Tag</th>
                    <td><input type="text" name="rte_saved[endatetime]" value="<?php echo $options['endatetime']; ?>" />
                    <p class="description">Required. The tag that surrounds the end date and time information. Expects to find date/time string in a valid format for strtotime().</p>
					</td>
                </tr>
                <tr valign="top"><th scope="row">Location Name Tag</th>
                    <td><input type="text" name="rte_saved[locname]" value="<?php echo $options['locname']; ?>" />
                    <p class="description">The tag that surrounds title of the location. Will be stored as a new Venue in the Events Calendar</p>
					</td>
                </tr>
                <tr valign="top"><th scope="row">Address Tag</th>
                    <td><input type="text" name="rte_saved[address]" value="<?php echo $options['address']; ?>" />
                    <p class="description">The tag that surrounds the address of the Venue. Will be parsed and stored as the Venue's address. Expects the format to be: street address, city, zip -- if not in this format it will not work. Consider using the address as the location title instead.</p>
					</td>
                </tr>
                <tr valign="top"><th scope="row">GUID</th>
                    <td><input type="text" name="rte_saved[guid]" value="<?php echo $options['guid']; ?>" />
                    <p class="description">The tags surrounding the GUID - The GUID MUST be a URL for the importer to prevent duplicate events. If it is not or you are unsure if the format is correct, LEAVE BLANK. </p>
					</td>
                </tr>
                <tr valign="top"><th scope="row">Event Info Link</th>
                    <td><input type="text" name="rte_saved[link]" value="<?php echo $options['link']; ?>" />
                    <p class="description">The tags surrounds a URL about the event. It will be saved as the Event Website.</p>
					</td>
                </tr>
                
            </table>
            <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
        </form>
    </div>