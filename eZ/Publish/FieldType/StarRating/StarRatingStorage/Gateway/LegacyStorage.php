<?php

namespace MakingWaves\eZStarRatingBundle\eZ\Publish\FieldType\StarRating\StarRatingStorage\Gateway;

use MakingWaves\eZStarRatingBundle\eZ\Publish\FieldType\StarRating\StarRatingStorage\Gateway;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\Core\Persistence\Database\DatabaseHandler;

class LegacyStorage extends Gateway
{
    protected $dbHandler;

    public function setConnection( $dbHandler )
    {
        if ( !$dbHandler instanceof DatabaseHandler )
        {
            throw new \RuntimeException( "Invalid dbHandler passed" );
        }

        $this->dbHandler = $dbHandler;
    }

    protected function getConnection()
    {
        if ( $this->dbHandler === null )
        {
            throw new \RuntimeException( "Missing database connection." );
        }
        return $this->dbHandler;
    }

    public function storeFieldData( Field $field )
    {
        $this->storeRatingData( $field->value->externalData );
    }

    public function getFieldData( Field $field )
    {
        $field->value->externalData = $this->getRatingData( $field->id );
    }

    public function deleteFieldData( $fieldId )
    {
        // Rating data should not be deleted here
    }

    public function getContentTypeId( Field $field )
    {
    }

    /**
     * Return all rating data for the specified ID
     *
     * @param integer $attributeID
     *
     * @return Aida data as a hash
     */
    protected function getRatingData( $attributeID )
    {
        $dbHandler = $this->getConnection();
        $query = $dbHandler->createSelectQuery();

        $query->select( '*' )
            ->from( $dbHandler->quoteTable( 'ezstarrating' ) )
            ->where(
                $query->expr->eq( 'contentobject_attribute_id', $attributeID )
            );

        $statement = $query->prepare();
        $statement->execute();
        $row = $statement->fetch( \PDO::FETCH_ASSOC );

        if ( $row === false )
        {
            // No rates found for this attribute
            return array( 'rating_average' => 0, 'rating_count' => 0 );
        }

        return $row;
    }

    // Nothing is currently stored from Symfony
    protected function storeRatingData( array $attributeArray )
    {
//        $dbHandler = $this->getConnection();

    }
}
