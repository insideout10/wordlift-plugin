/**
 * Components: Ranking Table Row.
 *
 * A {@link TableRow} which displays rankings.
 *
 * @since 3.20.0
 */
// External dependencies.
import React from "react";
import numeral from "numeral";

// Internal dependencies.
import TableRow from "../../../components/Table/TableRow";
import TableDataCell from "../../../components/Table/TableDataCell";

const RankingTableRow = ({ row: { keyword, rank, url, type, weight } }) => (
  <TableRow>
    <TableDataCell>
      <strong>{keyword}</strong>
    </TableDataCell>
    <TableDataCell style={{ textAlign: "right" }}>{rank}</TableDataCell>
    <TableDataCell>{url}</TableDataCell>
    <TableDataCell>{type}</TableDataCell>
    <TableDataCell>{numeral(weight).format("0.000")}</TableDataCell>
  </TableRow>
);

export default RankingTableRow;
