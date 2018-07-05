<?php

namespace JRC\binn\builders;

/**
 * BooleanBuilder provides values for True and False BinnContainers.
 *
 * @author jaredclemence
 */
class BooleanBuilder extends FixedResponseBuilder {
    public function __construct( $boolResult ) {
        $this->setFixedResponse( $boolResult );
    }
}
