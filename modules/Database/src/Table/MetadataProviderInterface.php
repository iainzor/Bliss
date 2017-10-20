<?php
namespace Database\Table;

interface MetadataProviderInterface
{
	public function populateMetadata(Metadata $metadata);
}