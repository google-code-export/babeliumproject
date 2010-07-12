﻿package commands.main{	/**	 * RED5 Open Source Flash Server - http://www.osflash.org/red5	 *	 * Copyright (c) 2006-2009 by respective authors (see below). All rights reserved.	 *	 * This library is free software; you can redistribute it and/or modify it under the	 * terms of the GNU Lesser General Public License as published by the Free Software	 * Foundation; either version 2.1 of the License, or (at your option) any later	 * version.	 *	 * This library is distributed in the hope that it will be useful, but WITHOUT ANY	 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A	 * PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.	 *	 * You should have received a copy of the GNU Lesser General Public License along	 * with this library; if not, write to the Free Software Foundation, Inc.,	 * 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA	 */	import business.NetConnectionDelegate;		import com.adobe.cairngorm.commands.ICommand;	import com.adobe.cairngorm.control.CairngormEvent;		import events.SetupConnectionEvent;		import model.DataModel;		import mx.rpc.IResponder;
	/**	 * Setup a new NetConnection with the RTMP server.	 * <p>The NetConnection is stored in the Model.</p>	 *	 * @see org.red5.samples.publisher.model.Media#nc nc	 * @author Thijs Triemstra, Babelium Team	 */	public class SetupConnectionCommand implements ICommand, IResponder	{		private var _dataModel:DataModel=DataModel.getInstance();		/**		 *		 * @param cgEvent		 */		public function execute(cgEvent:CairngormEvent):void		{			var event:SetupConnectionEvent=SetupConnectionEvent(cgEvent);			// Setup the permanent Delegate to manage the NetConnection.			_dataModel.netConnectionDelegate=new NetConnectionDelegate(this);		}		/**		 *		 * The result method is called when the delegate receives		 * a result from the service		 *		 * @param event		 */		public function result(event:Object):void		{			var info:Object=event.info;			var statusCode:String=info.code;			switch (statusCode)			{				case "NetConnection.Connect.Success":					//Set a flag in the model to denote the successful connection					_dataModel.netConnected=true;										// find out if it's a secure (HTTPS/TLS) connection					if (event.target.connectedProxyType == "HTTPS" || event.target.usingTLS)						trace("Connected to secure server");					else						trace("Connected to server");					break;				case "NetConnection.Connect.Failed":					_dataModel.netConnected = false;					trace("Connection to server failed");					break;				case "NetConnection.Connect.Closed":					_dataModel.netConnected = false;					trace("Connection to server closed");					break;				case "NetConnection.Connect.InvalidApp":					_dataModel.netConnected = false;					trace("Application not found on server");					break;				case "NetConnection.Connect.AppShutDown":					_dataModel.netConnected = false;					trace("Application has been shutdown");					break;				case "NetConnection.Connect.Rejected":					_dataModel.netConnected = false;					trace("No permissions to connect to the application");					break;				default:					// statements					break;			}		}		/**		 * The fault method is called when the delegate receives a fault from the service		 *		 * @param event		 */		public function fault(event:Object):void		{			trace(event.text);		}		/**		 * The Red5 oflaDemo returns bandwidth stats.		 */		public function onBWDone():void		{		}	}}