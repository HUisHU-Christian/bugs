<?php

class Ajax_Tags_Controller extends Base_Controller {

	public function get_suggestions($type = 'edit') {
		$retval = array();

		$term = Input::get('term', '');
		$term = (in_array($term, array("*"))) ? '%' : $term;
		if ($term) {
			$tags = Tag::where('tag', 'LIKE', '%' . $term . '%')->order_by('tag', 'ASC')->get();
			foreach ($tags as $tag) {
				if ($type == 'filter' && strpos($tag->tag, ':') !== false) {
					$tag_prefix = substr($tag->tag, 0, strpos($tag->tag, ':'));
				}
				$retval[] = $tag->tag;
			}
		}

		return json_encode($retval);
	}

}