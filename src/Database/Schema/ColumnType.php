<?php

namespace Spacio\Framework\Database\Schema;

enum ColumnType: string
{
    case Integer = 'INTEGER';
    case String = 'TEXT';
    case Text = 'TEXT';
    case Boolean = 'INTEGER';
    case Datetime = 'TEXT';
    case Date = 'TEXT';
    case Float = 'REAL';
    case Double = 'REAL';
}
